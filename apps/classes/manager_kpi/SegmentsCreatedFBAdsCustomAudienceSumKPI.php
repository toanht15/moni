<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.brandco.segment.SegmentActionBase');
class SegmentsCreatedFBAdsCustomAudienceSumKPI implements IManagerKPI {

    function doExecute($date) {
        $segment_action_logs = aafwEntityStoreFactory::create('SegmentActionLogs');

        $filter = array(
            'created_at:>=' => date("Y-m-d 00:00:00", strtotime($date)),
            'created_at:<=' => date("Y-m-d 23:59:59", strtotime($date)),
            'type' => SegmentActionLog::TYPE_ACTION_ADS,
        );

        return $segment_action_logs->count($filter);
    }
}