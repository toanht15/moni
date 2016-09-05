<?php
require_once dirname(__FILE__) . '/../../config/define.php';

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.AdsAudience');

class SendAdsTargetUser extends BrandcoBatchBase {

    private $ads_service;

    public function __construct($argv = null) {

        parent::__construct($argv);

        /** @var AdsService ads_service */
        $this->ads_service = $this->service_factory->create('AdsService');
    }

    public function executeProcess() {

        $relations = $this->ads_service->findAutoSendTargetRelations();

        foreach($relations as $relation) {
            $audience = $this->ads_service->findAdsAudiencesById($relation->ads_audience_id);
            if($audience->search_type == AdsAudience::SEACH_TYPE_SEGMENT) {
                $this->sendTargetDataFromSegment($audience, $relation);
            } else {
                $this->sendTargetDataFromAds($audience, $relation);
            }
        }
    }

    private function sendTargetDataFromSegment($ads_audience, $relation) {

        /** @var SegmentActionLogService $segment_action_log_service */
        $segment_action_log_service = $this->service_factory->create('SegmentActionLogService');
        $segment_action_log = $segment_action_log_service->findSegmentActionLogById($ads_audience->search_condition);

        $provision_id_array = $segment_action_log_service->convertSegmentProvisionIdsToProvisionIdArray($segment_action_log->segment_provison_ids);

        if(count($provision_id_array) == 0) {
            return;
        }

        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $brand_user_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $brand_user_relation = $brand_user_relation_service->getBrandsUsersRelationById($ads_audience->brand_user_relation_id);

        $page_info = array('brand_id' => $brand_user_relation->brand_id);

        $previous_date = strtotime('yesterday');
        $cur_date = strtotime('today');

        $create_date_array = array($previous_date, $cur_date);

        $search_condition[CpCreateSqlService::SEARCH_SEGMENT_CONDITION] = array(
            'create_dates' => $create_date_array,
            'provision_ids' => $provision_id_array,
        );

        $this->ads_service->removeTarget($search_condition,$page_info,array($relation));
        $this->ads_service->sendTarget($search_condition,$page_info,$segment_action_log->total,array($relation));
    }

    private function sendTargetDataFromAds($ads_audience, $relation) {

        $search_condition = json_decode($ads_audience->search_condition, true);

        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $brand_user_relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $brand_user_relation = $brand_user_relation_service->getBrandsUsersRelationById($ads_audience->brand_user_relation_id);

        $page_info = array('brand_id' => $brand_user_relation->brand_id);

        $this->ads_service->removeTarget($search_condition,$page_info,array($relation));
        $this->ads_service->sendTarget($search_condition,$page_info,0,array($relation));
    }
}