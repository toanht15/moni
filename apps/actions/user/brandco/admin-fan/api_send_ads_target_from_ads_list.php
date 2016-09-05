<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentActionTrait');

class api_send_ads_target_from_ads_list extends BrandcoPOSTActionBase {

    protected $ContainerName = 'ads_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $target_ads_account_ids;
    private $target_ads_relation_ids;

    const LIMIT_QUERY_COUNT = 10;

    public function doThisFirst() {

        parse_str($this->target_account_ids, $ads_account_id_parameter_array);

        foreach($ads_account_id_parameter_array as $key => $value) {
            if (strpos($key, 'ads_account') !== false) {
                $this->target_ads_account_ids = $value;
            }
        }

        parse_str($this->target_relation_ids, $ads_relation_id_parameter_array);

        foreach($ads_relation_id_parameter_array as $key => $value) {
            if (strpos($key, 'target_relation_ids') !== false) {
                $this->target_ads_relation_ids = $value;
            }
        }
    }

    public function validate() {

        if(count($this->target_ads_relation_ids) == 0) {
            return false;
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function doAction() {

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        foreach($this->target_ads_relation_ids as $relation_id) {

            $relation = $ads_service->findAudiencesAccountsRelationById($relation_id);

            if($relation) {
                $this->sendTarget($relation);
            }
        }

        $audiences = $this->fetchListAudience();

        $html = '';

        if($audiences['pager']['count'] > 0) {
            $html = aafwWidgets::getInstance()->loadWidget('AdsAudienceList')->render(
                array(
                    'brand_user_relation_id' => $this->getBrandsUsersRelation()->id,
                    'target_account_ids' => $this->target_ads_account_ids,
                    'audiences' => $audiences['list'],
                    'count' => $audiences['pager']['count'],
                    'page_no' => $this->page_no,
                    'count_per_page' => self::LIMIT_QUERY_COUNT,
                ));
        }

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    private function sendTarget($relation) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');
        $audience = $ads_service->findAdsAudiencesById($relation->ads_audience_id);

        $relation_array = array($relation);

        //Twitter場合、Email、IDを送信する
        if($relation->type != AdsAudiencesAccountsRelation::SEND_MIXED_TYPE) {

            $email_relation = $ads_service->findRelationByAccountIdAndAudienceIdAndType($relation->ads_account_id,$relation->ads_audience_id,AdsAudiencesAccountsRelation::SEND_ID_TYPE);

            if($email_relation) {
                $relation_array[] = $email_relation;
            }
        }

        if($audience->search_type == AdsAudience::SEACH_TYPE_SEGMENT) {
            $this->sendTargetDataFromSegment($audience, $relation_array);
        } else {
            $this->sendTargetDataFromAds($audience, $relation_array);
        }
    }

    private function sendTargetDataFromSegment($ads_audience, $relation_array) {

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
        $ads_service = $this->getService('AdsService');
        $ads_service->removeTarget($search_condition,$page_info,$relation_array);
        $ads_service->sendTarget($search_condition,$page_info,$segment_action_log->total,$relation_array);
    }

    private function sendTargetDataFromAds($ads_audience, $relation_array) {

        $search_condition = json_decode($ads_audience->search_condition, true);

        $page_info = array('brand_id' => $this->getBrand()->id);

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');
        $ads_service->removeTarget($search_condition,$page_info,$relation_array);
        $ads_service->sendTarget($search_condition,$page_info,0,$relation_array);

    }

    private function fetchListAudience() {

        $db = aafwDataBuilder::newBuilder();

        $conditions = $this->getConditions();
        $pager = $this->getPager();
        $order = $this->getOrder();

        $result = $db->getAdsAudiences($conditions,$order,$pager,true);

        return $result;
    }

    private function getConditions() {
        $conditions = array();

        $conditions['brand_user_relation_id'] = $this->getBrandsUsersRelation()->id;

        if($this->target_ads_account_ids) {
            $conditions['SEARCH_BY_ACCOUNT'] = '__ON__';
            $conditions['target_account_ids'] = $this->target_ads_account_ids;
        }

        return $conditions;
    }

    private function getPager() {

        if(Util::isNullOrEmpty($this->page_no)) {
            $this->page_no = 1;
        }

        $pager = array(
            'page'  => $this->page_no,
            'count' => self::LIMIT_QUERY_COUNT,
        );

        return $pager;
    }

    private function getOrder() {
        return array(
            'name' => 'r.updated_at',
            'direction' => 'DESC',
        );
    }
}
