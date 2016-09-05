<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpInstagramFollowActionLogService extends aafwServiceBase {

    protected $cp_ig_follow_action_log;

    public function __construct() {
        $this->cp_ig_follow_action_log = $this->getModel('CpInstagramFollowActionLogs');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @param $cooperate_status
     */
    public function createLog($cp_action_id, $cp_user_id, $cooperate_status) {
        $log = $this->cp_ig_follow_action_log->createEmptyObject();
        $log->cp_action_id = $cp_action_id;
        $log->cp_user_id = $cp_user_id;
        $log->cooperate_status = $cooperate_status;
        $this->cp_ig_follow_action_log->save($log);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @param $cooperate_status
     */
    public function createOnceLog($cp_action_id, $cp_user_id, $cooperate_status) {
        $log = $this->getLog($cp_action_id, $cp_user_id);
        if (!$log) {
            $this->createLog($cp_action_id, $cp_user_id, $cooperate_status);
        }
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @param $cooperate_status
     */
    public function updateLog($cp_action_id, $cp_user_id, $cooperate_status) {
        $log = $this->getLog($cp_action_id, $cp_user_id);
        $log->cooperate_status = $cooperate_status;
        $this->cp_ig_follow_action_log->save($log);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @param $cooperate_status
     */
    public function createOrUpdateLog($cp_action_id, $cp_user_id, $cooperate_status) {
        $log = $this->getLog($cp_action_id, $cp_user_id);
        if (!$log) {
            $this->createLog($cp_action_id, $cp_user_id, $cooperate_status);
        } else {
            $log->cooperate_status = $cooperate_status;
            $this->cp_ig_follow_action_log->save($log);
        }
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
        return $this->cp_ig_follow_action_log->findOne($filter);
    }

    public function deletePhysicalInstagramFollowActionLogsByCpActionId($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $logs = $this->cp_ig_follow_action_log->find(array("cp_action_id" => $cp_action_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_ig_follow_action_log->deletePhysical($log);
        }
    }

    public function deletePhysicalInstagramFollowActionLogsByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $logs = $this->cp_ig_follow_action_log->find(array("cp_action_id" => $cp_action_id, "cp_user_id" => $cp_user_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_ig_follow_action_log->deletePhysical($log);
        }
    }
}
