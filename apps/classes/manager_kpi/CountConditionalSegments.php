<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.brandco.segment.SegmentActionBase');
class CountConditionalSegments implements IManagerKPI {

    function doExecute($date) {
        $conditionalsegments = aafwEntityStoreFactory::create('Segments');

        $filter = array(
            'created_at:>=' => date("Y-m-d 00:00:00", strtotime($date)),
            'created_at:<=' => date("Y-m-d 23:59:59", strtotime($date)),
            'status' => Segment::STATUS_ACTIVE,
            'type' => Segment::TYPE_CONDITIONAL_SEGMENT,
            'archive_flg' => Segment::ARCHIVE_OFF,
        );
        return $conditionalsegments->count($filter);
    }
}
