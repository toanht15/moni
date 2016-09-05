<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UpdateCountBatchLogService extends aafwServiceBase
{
    private $update_count_batch_logs;

    public function __construct() {
        $this->update_count_batch_logs = $this->getModel('UpdateCountBatchLogs');
    }

    public function getBatchLog($batchName, $date, $status) {
        $filter = array(
            'batch_name' => $batchName,
            'status' => $status,
            'created_at:<=' => $date.' 23:59:59',
            'created_at:>=' => $date.' 00:00:00',
        );
        return $this->update_count_batch_logs->findOne($filter);
    }

    public function saveBatchLog($batchName,$status) {
        $updateCountBatchLog = $this->update_count_batch_logs->createEmptyObject();
        $updateCountBatchLog->batch_name = $batchName;
        $updateCountBatchLog->status = $status;
        $this->update_count_batch_logs->save($updateCountBatchLog);
    }
}