<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * Class CpLineAddFriendActionLogService
 * このテープルcp_line_add_friend_action_logsに関する操作（Insert、Update、Delete）をここで実施する
 *
 */
class CpLineAddFriendActionLogService extends aafwServiceBase {

    protected $cp_line_add_friend_action_log_store;
    private $logger;

    public function __construct() {

        /** @var CpLineAddFriendActionLogs cp_line_add_friend_action_log_store */
        $this->cp_line_add_friend_action_log_store = $this->getModel('CpLineAddFriendActionLogs');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_line_add_friend_action_id
     * @param $cp_user_id
     * @return CpLineAddFriendActionLog
     */
    public function findLogByCpActionIdAndCpUserId($cp_line_add_friend_action_id, $cp_user_id) {

        if(!$cp_line_add_friend_action_id || !$cp_user_id) {
            return null;
        }

        $filter = array(
            'cp_line_add_friend_action_id' => $cp_line_add_friend_action_id,
            'cp_user_id' => $cp_user_id
        );

        return $this->cp_line_add_friend_action_log_store->findOne($filter);
    }
    
    /**
     * @param $cp_line_add_friend_action_id
     * @return array
     */
    public function findLogsByCpActionId($cp_line_add_friend_action_id) {

        if(!$cp_line_add_friend_action_id) {
            return null;
        }

        $filter = array(
            'cp_line_add_friend_action_id' => $cp_line_add_friend_action_id
        );

        return $this->cp_line_add_friend_action_log_store->find($filter);
    }

    /**
     * @param $cp_line_add_friend_action_id
     * @param $cp_user_id
     */
    public function createLog($cp_line_add_friend_action_id, $cp_user_id) {
        $cp_line_add_friend_action_log = $this->cp_line_add_friend_action_log_store->createEmptyObject();
        $cp_line_add_friend_action_log->cp_user_id = $cp_user_id;
        $cp_line_add_friend_action_log->cp_line_add_friend_action_id = $cp_line_add_friend_action_id;

        $this->cp_line_add_friend_action_log_store->save($cp_line_add_friend_action_log);
    }

    /**
     * @param $cp_line_add_friend_action_id
     */
    public function deletePhysicalLogByCpActionId ($cp_line_add_friend_action_id) {

        $logs = $this->findLogsByCpActionId($cp_line_add_friend_action_id);

        if (!$logs) {
            return;
        }

        foreach ($logs as $log) {
            $this->cp_line_add_friend_action_log_store->deletePhysical($log);
        }
    }

    /**
     * @param $cp_line_add_friend_action_id
     * @param $cp_user_id
     */
    public function deletePhysicalLogsByCpActionIdAndCpUserId ($cp_line_add_friend_action_id, $cp_user_id) {

        $log = $this->findLogByCpActionIdAndCpUserId($cp_line_add_friend_action_id, $cp_user_id);

        if (!$log) {
            return;
        }

        $this->cp_line_add_friend_action_log_store->deletePhysical($log);
    }
}
