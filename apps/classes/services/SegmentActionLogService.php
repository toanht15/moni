<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SegmentActionLogService extends aafwServiceBase {

    private $segment_action_log_store;

    public function __construct() {
        /** @var SegmentActionLogs segment_action_log_store */
        $this->segment_action_log_store = $this->getModel('SegmentActionLogs');
    }

    public function createSegmentActionLog($log_data) {

        $segment_action_log = $this->segment_action_log_store->createEmptyObject();

        $segment_action_log->brand_id = $log_data['brand_id'];
        $segment_action_log->segment_provison_ids = $log_data['segment_provison_ids'];
        $segment_action_log->type = $log_data['type'];
        $segment_action_log->total = $log_data['total'];
        $segment_action_log->del_flg = 0;

        return $this->segment_action_log_store->save($segment_action_log);
    }

    /**
     * @param $segment_action_log_id
     * @return mixed
     */
    public function findSegmentActionLogById($segment_action_log_id) {

        $filter = array(
            'id' => $segment_action_log_id
        );

        return $this->segment_action_log_store->findOne($filter);
    }

    /**
     * @param $sp_ids_json
     * @return array
     */
    public function convertSegmentProvisionIdsToProvisionIdArray($sp_ids_json) {
        $provision_ids = array();

        $sp_ids = json_decode($sp_ids_json, true);

        foreach($sp_ids as $value) {

            foreach($value as $provision_id) {

                $provision_ids[] = $provision_id;
            }

        }
        return $provision_ids;
    }
}
