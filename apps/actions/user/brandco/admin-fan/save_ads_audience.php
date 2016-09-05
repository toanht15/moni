<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_ads_audience extends BrandcoPOSTActionBase {

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'create_ads_audience';

    protected $Form = array(
        'package' => 'admin-fan',
        'action' => 'create_ads_audience?mid=failed',
    );

    protected $ValidatorDefinition = array(
        'audience_name' => array(
            'type' => 'str',
            'required' => true
        )
    );

    public function validate() {

        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        if($this->save_type == AdsAudience::STATUS_ACTIVE) {

            $this->ValidatorDefinition['ads_account_ids'] = array(
                'required' => true
            );

            foreach($this->ads_account_ids as $ads_account_id) {
                if(!$validator->isValidAdsAccountId($ads_account_id)) {
                    return '404';
                }
            }
        }

        return true;
    }

    public function doAction() {

        $transaction = aafwEntityStoreFactory::create('AdsAudiences');

        try {
            $transaction->begin();
            $ads_audience = $this->saveAdsAudience();

            if($this->save_type == AdsAudience::STATUS_ACTIVE) {
                /** @var AdsService $ads_service */
                $ads_service = $this->createService('AdsService');
                $ads_accounts = $ads_service->findAdsAccountsByIds($this->ads_account_ids);
                $relations = $this->createAudienceAccountRelation($ads_audience, $ads_accounts);
                $this->sendTarget($ads_audience, $relations);
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return 'redirect: ' . Util::rewriteUrl('admin-fan', 'create_ads_audience', array(), array('mid'=>'failed'));
        }

        $this->Data["saved"] = true;

        if($this->save_type == AdsAudience::STATUS_ACTIVE) {
            $redirect_url = Util::rewriteUrl('admin-fan', 'ads_list', array(), array('mid' => 'send-target-user-success'));
        } else {
            $redirect_url = Util::rewriteUrl('admin-fan', 'ads_audience', array($ads_audience->id), array('mid' => 'action-draft'));
        }

        return 'redirect: ' . $redirect_url;
    }

    /**
     * @return mixed
     */
    private function saveAdsAudience() {

        $audience_data = array();

        /** @var SegmentService $segment_service */
        $segment_service = $this->createService('SegmentService');
        $audience_data['search_condition'] = $segment_service->getSegmentProvision($this->POST['spc'][0]);

        $audience_data['brand_user_relation_id'] = $this->getBrandsUsersRelation()->id;
        $audience_data['name'] = $this->audience_name;

        if($this->description_flg) {
            $audience_data['description'] = $this->audience_description;
        } else {
            $audience_data['description'] = '';
        }

        $audience_data['search_type'] = AdsAudience::SEACH_TYPE_ADS;
        $audience_data['status'] = $this->save_type;

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        return $ads_service->createOrUpdateAdsAudience($audience_data);
    }

    /**
     * @param $audience
     * @param $accounts
     * @return array
     */
    public function createAudienceAccountRelation($audience, $accounts) {

        $relations = array();

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        foreach($accounts as $account) {
            $relation = $ads_service->createAudiencesAccountsRelation($audience, $account);
            if($relation) {
                $relations = array_merge($relations, $relation);
            }
        }

        return $relations;
    }

    private function sendTarget($ads_audience, $relations) {

        try {

            $search_condition = json_decode($ads_audience->search_condition, true);
            $page_info = array('brand_id' => $this->getBrand()->id);

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_service->sendTarget($search_condition,$page_info,0,$relations);

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            throw $e;
        }
    }
}
