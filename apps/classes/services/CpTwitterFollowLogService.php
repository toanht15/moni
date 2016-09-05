<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpTwitterFollowLogService extends aafwServiceBase {

    protected $cp_tw_follow_log_service;
    private $logger;

    public function __construct() {
        $this->cp_tw_follow_log_service = $this->getModel('CpTwitterFollowLogs');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_user_id
     * @param $cp_twitter_follow_action_id
     * @param $status
     */
    public function create($cp_user_id, $cp_twitter_follow_action_id, $status) {
        $follow_log = $this->cp_tw_follow_log_service->createEmptyObject();
        $follow_log->action_id = $cp_twitter_follow_action_id;
        $follow_log->cp_user_id = $cp_user_id;
        $follow_log->status = $status;
        $this->cp_tw_follow_log_service->save($follow_log);
    }

    public function getLogByCpUserIdAndActionId($cp_user_id, $cp_twitter_follow_action_id) {
        $log = $this->cp_tw_follow_log_service->findOne(array(
            'cp_user_id' => $cp_user_id,
            'action_id'  => $cp_twitter_follow_action_id,
            'status: <>' => CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING
        ));

        return $log;
    }

    public function getConnectingLogByCpUserIdAndActionId($cp_user_id, $cp_twitter_follow_action_id) {
        $log = $this->cp_tw_follow_log_service->findOne(array(
            'cp_user_id' => $cp_user_id,
            'action_id'  => $cp_twitter_follow_action_id,
            'status'     => CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING
        ));

        return $log;
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalFollowLogsByConcreteActionId($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $logs = $this->cp_tw_follow_log_service->find(array('action_id' => $cp_action_id));
        if (!$logs) {
            return;
        }

        foreach ($logs as $log) {
            $this->cp_tw_follow_log_service->deletePhysical($log);
        }
    }

    public function deletePhysicalFollowLogsByConcreteActionIdAndCpUserId($cp_action_id, $cp_user_id)
    {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $logs = $this->cp_tw_follow_log_service->find(array('action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$logs) {
            return;
        }

        foreach ($logs as $log) {
            $this->cp_tw_follow_log_service->deletePhysical($log);
        }
    }

    public function getCpTwFollowLogWithStatusStringByIds($cp_user_id, $cp_twitter_follow_action_id) {
        $cp_tw_follow_log = $this->getLogByCpUserIdAndActionId($cp_user_id, $cp_twitter_follow_action_id);
        $cp_tw_follow_log->status = CpTwitterFollowLog::$tw_follow_statuses[$cp_tw_follow_log->status];
        return $cp_tw_follow_log;
    }

    public function getCpTwFollowLogsByCpUserListAndCpActionId($cp_user_ids, $cp_twitter_follow_action_id) {
        $result = array();
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_ids,
                'action_id'  => $cp_twitter_follow_action_id,
                'status: <>' => CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING
            ),
        );
        $cp_tw_follow_logs = $this->cp_tw_follow_log_service->find($filter);
        foreach ($cp_tw_follow_logs as $element) {
            $result[$element->cp_user_id]->status_string = CpTwitterFollowLog::$tw_follow_statuses[$element->status];
        }
        return $result;
    }
}
