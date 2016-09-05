<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpInstagramFollowUserLogService extends aafwServiceBase {

    protected $cp_ig_follow_user_log;

    public function __construct() {
        $this->cp_ig_follow_user_log = $this->getModel('CpInstagramFollowUserLogs');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @return mixed
     */
    public function getLog($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->cp_ig_follow_user_log->findOne($filter);
    }

    /**
     * @return mixed
     */
    public function createEmptyLog() {
        return $this->cp_ig_follow_user_log->createEmptyObject();
    }

    /**
     * @param CpInstagramFollowUserLog $log
     */
    public function saveLog(CpInstagramFollowUserLog $log) {
        $this->cp_ig_follow_user_log->save($log);
    }

    /**
     * フォローチェック用
     * @return mixed
     */
    public function getCpInstargamFollowUserLogsForUpdate() {
        $filter = array(
            'follow_status' => CpInstagramFollowUserLog::NOT_FOLLOWED,
            'check_flg' => CpInstagramFollowUserLog::UNCHECKED,
            'created_at:<=' => date('Y-m-d H:i:s' , strtotime('-1 hour'))
        );

        return $this->cp_ig_follow_user_log->find($filter);
    }

    public function deletePhysicalInstagramFlowUserLogsByCpActionId ($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $logs = $this->cp_ig_follow_user_log->find(array('cp_action_id' => $cp_action_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_ig_follow_user_log->deletePhysical($log);
        }
    }

    public function deletePhysicalInstagramFlowUserLogsByCpActionIdAndCpUserId ($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $logs = $this->cp_ig_follow_user_log->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_ig_follow_user_log->deletePhysical($log);
        }
    }
}
