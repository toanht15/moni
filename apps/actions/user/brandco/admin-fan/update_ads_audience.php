<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');

class update_ads_audience extends BrandcoPOSTActionBase {

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'ads_audience';
    protected $Form = array(
        'package' => 'admin-fan',
        'action' => 'ads_audience/{audience_id}',
    );

    protected $ValidatorDefinition = array(
        'audience_id' => array(
            'type' => 'num',
            'required' => true
        ),
        'audience_name' => array(
            'type' => 'str',
            'required' => true
        )
    );

    public function validate() {

        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        if (!$validator->isValidAdsAudienceId($this->audience_id)) {
            return '404';
        }

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
            /** @var AdsService $ads_service */
            $ads_service = $this->getService("AdsService");
            $audience = $ads_service->findAdsAudiencesById($this->audience_id);
            $audience = $this->updateAdsAudience($audience);

            if($this->save_type == AdsAudience::STATUS_ACTIVE) {
                $ads_accounts = $ads_service->findAdsAccountsByIds($this->ads_account_ids);
                $relations = $this->createOrUpdateAudienceAccountRelation($audience, $ads_accounts);
                $this->sendTarget($audience, $relations);
            }

            $transaction->commit();

        } catch (Exception $e) {

            $transaction->rollback();

            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return 'redirect: ' . Util::rewriteUrl('admin-fan', 'ads_audience', array($this->audience_id), array("mid"=>"send-target-user-fail"));
        }

        $this->Data["saved"] = true;

        if($this->save_type == AdsAudience::STATUS_ACTIVE) {
            $redirect_url = Util::rewriteUrl('admin-fan', 'ads_list', array(), array('mid' => 'send-target-user-success'));
        } else {
            $redirect_url = Util::rewriteUrl('admin-fan', 'ads_audience', array($this->audience_id), array('mid' => 'action-draft'));
        }

        return 'redirect: ' . $redirect_url;
    }

    /**
     * @return mixed
     */
    private function updateAdsAudience($audience) {

        $audience_data = array();

        if($audience->search_type == AdsAudience::SEACH_TYPE_ADS) {
            /** @var SegmentService $segment_service */
            $segment_service = $this->createService('SegmentService');
            $audience_data['search_condition'] = $segment_service->getSegmentProvision($this->POST['spc'][0]);
        } else {
            $audience_data['search_condition'] = $audience->search_condition;
        }

        $audience_data['id'] = $audience->id;
        $audience_data['brand_user_relation_id'] = $audience->brand_user_relation_id;
        $audience_data['name'] = $this->audience_name;

        if($this->description_flg) {
            $audience_data['description'] = $this->audience_description;
        } else {
            $audience_data['description'] = '';
        }

        $audience_data['search_type'] = $audience->search_type;
        $audience_data['status'] = $this->save_type == AdsAudience::STATUS_ACTIVE ?: $audience->status;

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');
        return $ads_service->createOrUpdateAdsAudience($audience_data);
    }

    /**
     * @param $audience
     * @param $accounts
     * @return array
     */
    public function createOrUpdateAudienceAccountRelation($audience, $accounts) {

        $relations = array();
        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        foreach($accounts as $account) {

            $result = $ads_service->findAudiencesAccountsRelationsByAccountIdAndAudienceId($account->id, $audience->id);

            if($result) {

                //Facebookアカウントなら、オーディエンスを更新する
                if($account->isFacebookAccount()) {
                    $relation_array = array($ads_service->updateFacebookRelation($audience, $account));
                } else {
                    $relation_array = $result->toArray();
                }

            } else {
                $relation_array = $ads_service->createAudiencesAccountsRelation($audience, $account);
            }

            $relations = array_merge($relations, $relation_array);
        }

        return $relations;
    }

    private function sendTarget($ads_audience, $relations) {
        if($ads_audience->search_type == AdsAudience::SEACH_TYPE_ADS) {
            $this->sendTargetDataFromAds($ads_audience, $relations);
        } elseif($ads_audience->search_type == AdsAudience::SEACH_TYPE_SEGMENT) {
            $this->sendTargetDataFromSegment($ads_audience, $relations);
        }
    }

    private function sendTargetDataFromSegment($ads_audience, $relations) {

        try {
            /** @var SegmentActionLogService $segment_action_log_service */
            $segment_action_log_service = $this->createService('SegmentActionLogService');
            $segment_action_log = $segment_action_log_service->findSegmentActionLogById($ads_audience->search_condition);

            $provision_id_array = $segment_action_log_service->convertSegmentProvisionIdsToProvisionIdArray($segment_action_log->segment_provison_ids);

            if(count($provision_id_array) == 0) {
                return;
            }

            $page_info = array('brand_id' => $this->getBrand()->id);

            $previous_date = strtotime('yesterday');
            $cur_date = strtotime('today');

            $create_date_array = array($previous_date, $cur_date);

            $search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION] = array(
                'create_dates' => $create_date_array,
                'provision_ids' => $provision_id_array,
            );

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_service->removeTarget($search_condition,$page_info,$relations);
            $ads_service->sendTarget($search_condition,$page_info,$segment_action_log->total,$relations);

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            throw $e;
        }
    }

    private function sendTargetDataFromAds($ads_audience, $relations) {
        try {

            $search_condition = json_decode($ads_audience->search_condition, true);

            $page_info = array('brand_id' => $this->getBrand()->id);

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_service->removeTarget($search_condition,$page_info,$relations);
            $ads_service->sendTarget($search_condition,$page_info,0,$relations);
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            throw $e;
        }
    }
}