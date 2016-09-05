<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpShareUserLogService extends aafwServiceBase {

    /** @var CpShareUserLogs $cp_share_user_logs */
    protected $cp_share_user_logs;

    public function __construct() {
        $this->cp_share_user_logs = $this->getModel('CpShareUserLogs');
    }

    /**
     * cp_share_user_logにレコードを追加する。もし既にレコードが存在していたら更新する。
     * @param $cp_user_id
     * @param $cp_share_action_id
     * @param $type
     * @param $text
     * @return mixed
     * @throws aafwException
     */
    public function createOrUpdate($cp_user_id, $cp_share_action_id, $type, $text = null) {
        $cp_share_user_log = $this->getCpShareUserLog($cp_user_id) ? : $this->cp_share_user_logs->createEmptyObject();
        $cp_share_user_log->cp_user_id = $cp_user_id;
        $cp_share_user_log->cp_share_action_id = $cp_share_action_id;
        $cp_share_user_log->type = $type;
        $cp_share_user_log->text = $text;
        return $this->cp_share_user_logs->save($cp_share_user_log);
    }

    /**
     * @param $cp_user_id
     * @return entity
     */
    public function getCpShareUserLog($cp_user_id) {
        $filter = array(
            'cp_user_id' => $cp_user_id
        );
        return $this->cp_share_user_logs->findOne($filter);
    }

    public function getCpShareUserLogByIds($cp_user_id, $cp_share_action_id) {
        $filter = array(
            'cp_user_id' => $cp_user_id,
            'cp_share_action_id' => $cp_share_action_id,
        );

        return $this->cp_share_user_logs->findOne($filter);
    }

    public function getCpShareUserLogWithStatusStringByIds($cp_user_id, $cp_share_action_id) {
        $cp_share_user_log = $this->getCpShareUserLogByIds($cp_user_id, $cp_share_action_id);

        if ($cp_share_user_log) {
            switch ($cp_share_user_log->type) {
                case CpShareUserLog::TYPE_SHARE:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_SHARE;
                    break;
                case CpShareUserLog::TYPE_SKIP:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_SKIP;
                    break;
                case CpShareUserLog::TYPE_UNREAD:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_UNREAD;
                    break;
                case CpShareUserLog::TYPE_ERROR:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_ERROR;
                    break;
            }
        }

        return $cp_share_user_log;
    }
    
    public function getCpShareUserLogByCpShareActionIdAndFanListUser($cp_share_action_id, $cp_user_ids) {
        $filter = array(
            'cp_share_action_id' => $cp_share_action_id,
            'cp_user_id' => $cp_user_ids,
        );
        return $this->cp_share_user_logs->find($filter);
    }

    /**
     * @param $cp_share_action_id
     */
    public function deletePhysicalShareUserLogByShareActionId ($cp_share_action_id) {

        if (!$cp_share_action_id) {
            return;
        }

        $logs = $this->cp_share_user_logs->find(array('cp_share_action_id' => $cp_share_action_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_share_user_logs->deletePhysical($log);
        }
    }

    public function deletePhysicalShareUserLogByShareActionIdAndCpUserId ($cp_share_action_id, $cp_user_id)
    {

        if (!$cp_share_action_id || !$cp_user_id) {
            return;
        }

        $logs = $this->cp_share_user_logs->find(array('cp_share_action_id' => $cp_share_action_id, 'cp_user_id' => $cp_user_id));
        if (!$logs) {
            return;
        }
        foreach ($logs as $log) {
            $this->cp_share_user_logs->deletePhysical($log);
        }
    }

    public function getListShareLogOfUser($list_user_share_logs) {
        $user_share_log_array = array();
        foreach ($list_user_share_logs as $cp_share_user_log) {
            switch ($cp_share_user_log->type) {
                case CpShareUserLog::TYPE_SHARE:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_SHARE;
                    break;
                case CpShareUserLog::TYPE_SKIP:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_SKIP;
                    break;
                case CpShareUserLog::TYPE_UNREAD:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_UNREAD;
                    break;
                case CpShareUserLog::TYPE_ERROR:
                    $cp_share_user_log->type = CpShareUserLog::STATUS_ERROR;
                    break;
            }
            $user_share_log_array[$cp_share_user_log->cp_user_id] = $cp_share_user_log;
        }
        return $user_share_log_array;
    }
}