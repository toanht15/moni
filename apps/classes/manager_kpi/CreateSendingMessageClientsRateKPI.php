<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CreateSendingMessageClientsRateKPI implements IManagerKPI {

    function doExecute($date) {
        $filter = array(
            'status' => Segment::STATUS_ACTIVE,
            'archive_flg' => Segment::ARCHIVE_OFF,
        );

        $mainte_db = new aafwDataBuilder('maintedb');
        $clients_creates_egment = $mainte_db->getCountBrandCreatedSegments($filter);


        $sending_message_filter = array(
            'created_at_start' => date("Y-m-d 00:00:00", strtotime($date)),
            'created_at_end' => date("Y-m-d 23:59:59", strtotime($date)),
            'type' => SegmentActionLog::TYPE_ACTION_MESSAGE,
        );

        $clients_create_sending_message = $mainte_db->getCountClientsCreatedSegmentAction($sending_message_filter);

        $result = $clients_create_sending_message[0]['total'] / $clients_creates_egment[0]['total'] * 100;
        return $result;
    }
}