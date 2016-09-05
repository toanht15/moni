<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentAdsAction extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');
        $ads_users = $ads_service->findAdsUsersByBrandUserRelationId($params['brand_user_relation_id']);

        $ads_service->updateAdsAccountInfo($ads_users);

        $params["ads_accounts"] = $ads_service->findValidAdsAccountByBrandUserRelationId($params['brand_user_relation_id']);

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');
        $params['provision_ids'] = $segment_service->getProvisionIdsFromSession($params['segment_provisions']);

        return $params;
    }
}
