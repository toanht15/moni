<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentMessageActionConditionList extends aafwWidgetBase {

    public function doService($params = array()) {

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');

        $params['segments'] = $segment_service->getActiveSegmentsByBrandId($params['brand']->id);

        $params['segment_provision_sessions'] = $this->findSegmentProvisionById($params['segment_condition_session'], $segment_service);
        
        $params['provision_id_array'] = $segment_service->getProvisionIdsFromSession($params['segment_condition_session']);

        $params['target_user_count'] = $segment_service->calculateSegmentProvisionUserCount($params['provision_id_array']);

        return $params;
    }

    public function findSegmentProvisionById($segment_condition_session, $segment_service) {
        $segment_provisions = array();
        foreach($segment_condition_session as $segment_id => $segment_provision_ids) {
            foreach($segment_provision_ids as $provision_id) {
                $provision = $segment_service->getSegmentProvisionById($provision_id);

                if($provision->name == '') {
                    $segment = $segment_service->getSegmentById($segment_id);
                    $provision->name = $segment->name;
                }

                $segment_provisions[] = $provision;
            }
        }
        return $segment_provisions;
    }
}