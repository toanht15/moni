<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.brandco.segment.SegmentActionBase');
class CreateFBAdsCustomAudienceClientsRateKPI implements IManagerKPI {

    function doExecute($date) {
        $filter = array(
            'status' => Segment::STATUS_ACTIVE,
            'archive_flg' => Segment::ARCHIVE_OFF,
        );

        $mainte_db = new aafwDataBuilder('maintedb');
        $clients_create_segment = $mainte_db->getCountBrandCreatedSegments($filter);


        $fbadscustomaudience_filter = array(
            'created_at_start' => date("Y-m-d 00:00:00", strtotime($date)),
            'created_at_end' => date("Y-m-d 23:59:59", strtotime($date)),
            'type' => SegmentActionLog::TYPE_ACTION_ADS,
        );

        $clients_create_fbads_customaudience = $mainte_db->getCountClientsCreatedSegmentAction($fbadscustomaudience_filter);

        $result = $clients_create_fbads_customaudience[0]['total'] / $clients_create_segment[0]['total']  * 100;
        return $result;
    }
}