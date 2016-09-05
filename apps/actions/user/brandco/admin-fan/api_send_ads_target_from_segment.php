<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentActionTrait');

class api_send_ads_target_from_segment extends BrandcoPOSTActionBase {

    use SegmentActionTrait;

    protected $ContainerName = 'segment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $sp_ids_array;
    private $ads_account_id_array;

    public function validate() {

        parse_str($this->ads_account_ids, $ads_account_id_parameter_array);

        foreach($ads_account_id_parameter_array as $key => $value) {
            if (strpos($key, 'ads_account_ids') !== false) {
                $this->ads_account_id_array = $value;
            }
        }

        if(Util::isNullOrEmpty($this->ads_account_id_array)) {
            return false;
        }

        if(Util::isNullOrEmpty($this->ads_audience_name)) {
            return false;
        }

        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        foreach($this->ads_account_id_array as $account_id) {

            if (!$validator->isValidAdsAccountId($account_id)) {
                return false;
            }
        }

        $is_valid = false;

        parse_str($this->target_data,$sp_parameter_array);

        foreach ($sp_parameter_array as $key => $value) {
            if (strpos($key, 'sp_ids_') !== false) {
                $is_valid = true;
                $temp_rs = explode('sp_ids_', $key);
                $this->sp_ids_array[$temp_rs[1]] = $value;
            }
        }

        if (!$is_valid) {
            return false;
        }

        if($this->isContainInvalidSegment(array_keys($this->sp_ids_array), $this->getBrand()->id)) {
            return false;
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function sendTarget($target_count, $relations) {
        try {
            $page_info = array('brand_id' => $this->getBrand()->id);

            $provision_id_array = $this->getProvisionIdArray();

            $previous_date = strtotime('yesterday');
            $cur_date = strtotime('today');

            $create_date_array = array($previous_date, $cur_date);

            $search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION] = array(
                'create_dates' => $create_date_array,
                'provision_ids' => $provision_id_array,
            );

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_service->sendTarget($search_condition,$page_info,$target_count,$relations);

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            throw $e;
        }
    }

    public function doAction() {

        $transaction = aafwEntityStoreFactory::create('SegmentActionLogs');

        try {
            $transaction->begin();

            //Save Segment Log
            $segment_action_log = $this->saveSegmentActionLog(SegmentActionLog::TYPE_ACTION_ADS);

            //Create New Audience
            $ads_audience = $this->createAdsAudience($segment_action_log->id);

            /** @var AdsService $ads_service */
            $ads_service = $this->createService('AdsService');
            $ads_accounts = $ads_service->findAdsAccountsByIds($this->ads_account_id_array);
            $relations = $this->createAudienceAccountRelation($ads_audience, $ads_accounts);

            $this->sendTarget($segment_action_log->total, $relations);

            $transaction->commit();

            $parser = new PHPParser();
            $html = $this->sanitizeOutput($parser->parseTemplate(
                'ads/SegmentAdsActionConfirmModal.php', array(
                    'ads_accounts' => $ads_accounts,
                    'ads_audience' => $ads_audience,
                    'provision_ids' => $this->getProvisionIdArray()
                )
            ));

            $json_data = $this->createAjaxResponse("ok", array(), array(), $html);

        } catch (Exception $e) {
            $transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * @return mixed
     */
    private function createAdsAudience($segment_action_log_id) {

        $audience_data = array();

        $audience_data['search_condition'] = $segment_action_log_id;
        $audience_data['brand_user_relation_id'] = $this->getBrandsUsersRelation()->id;
        $audience_data['name'] = $this->ads_audience_name;

        if($this->description_flg) {
            $audience_data['description'] = $this->ads_audience_description;
        } else {
            $audience_data['description'] = '';
        }

        $audience_data['search_type'] = AdsAudience::SEACH_TYPE_SEGMENT;
        $audience_data['status'] = AdsAudience::STATUS_ACTIVE;

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

    private function getProvisionIdArray() {

        $provision_id_array = array();

        foreach($this->sp_ids_array as $provision_ids) {
            foreach($provision_ids as $provision_id) {
                $provision_id_array[] = $provision_id;
            }
        }

        return $provision_id_array;
    }

    private function sanitizeOutput($html) {

        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        $html = preg_replace($search, $replace, $html);

        return $html;
    }
}
