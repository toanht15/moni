<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpPageViewLogService extends aafwServiceBase {

    protected $cp_page_view_log_store;

    public function __construct() {
        $this->cp_page_view_log_store = $this->getModel("CpPageViewLogs");
    }

    /**
     * @param $cp_id
     * @param $status
     */
    public function updateCpPageViewLog($cp_id, $status) {
        $cp_page_view_log = $this->getCpPageViewLogByCpId($cp_id);
        if (!$cp_page_view_log) {
            $cp_page_view_log = $this->createEmptyObject();
        }
        $cp_page_view_log->cp_id = $cp_id;
        $cp_page_view_log->status = $status;

        $this->cp_page_view_log_store->save($cp_page_view_log);
    }

    /**
     * @param $cp_id
     * @return mixed
     */
    public function getCpPageViewLogByCpId($cp_id) {
        $filter = array(
            "cp_id" => $cp_id
        );

        return $this->cp_page_view_log_store->findOne($filter);
    }

    /**
     * @return mixed
     */
    public function createEmptyObject() {
        return $this->cp_page_view_log_store->createEmptyObject();
    }

    /**
     * @param $cp_id
     * @param $status
     * @return mixed
     */
    public function getCpPageViewLogByCpIdAndStatus($cp_id, $status) {
        $filter = array(
            "cp_id" => $cp_id,
            "status" => $status
        );

        return $this->cp_page_view_log_store->find($filter);
    }
}