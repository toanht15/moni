<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CodeAuthUserTrackingService extends aafwServiceBase {

    private $code_auth_track_logs;

    public function __construct() {
        $this->code_auth_track_logs = $this->getModel('CodeAuthUserTrackingLogs');
    }

    public function createEmptyTrackLog() {
        return $this->code_auth_track_logs->createEmptyObject();
    }

    public function updateTrackLog($track_log) {
        $this->code_auth_track_logs->save($track_log);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getTrackingUserByUserAnCpActionId($user_id, $cp_action_id) {
        $filter = array(
            'user_id' => $user_id,
            'cp_action_id' => $cp_action_id
        );

        return $this->code_auth_track_logs->findOne($filter);
    }

    /**
     * @param $user_id
     * @param $cp_action_id
     * @return bool|mixed
     */
    public function trackingUser($user_id, $cp_action_id) {
        $track_log = $this->getTrackingUserByUserAnCpActionId($user_id, $cp_action_id);

        if (!$track_log) {
            $track_log = $this->createEmptyTrackLog();
            $track_log->user_id = $user_id;
            $track_log->cp_action_id = $cp_action_id;
        }

        if ($this->isPast($track_log->acc_locking_expire_date)) {
            $track_log->auth_error_count = 0;
        } else if ($track_log->auth_error_count >= 3) {
            return false;
        }

        $date = new DateTime();
        $date->add(new DateInterval('PT1H'));

        $track_log->auth_error_count += 1;
        $track_log->acc_locking_expire_date = $date->format('Y-m-d H:i:s');

        $this->updateTrackLog($track_log);

        return $track_log;
    }

    /**
     * @param $user_id
     * @param $cp_action_id
     * @return bool|mixed
     */
    public function untrackUser($user_id, $cp_action_id) {
        $track_log = $this->getTrackingUserByUserAnCpActionId($user_id, $cp_action_id);

        if (!$track_log) return false;

        $track_log->auth_error_count = 0;
        $track_log->acc_locking_expire_date = '0000-00-00 00:00:00';

        $this->updateTrackLog($track_log);
    }

    /**
     * @param $track_log
     * @return bool
     */
    public function isLockingUser($track_log) {
        return $track_log && $track_log->auth_error_count >= 3 && !$this->isPast($track_log->acc_locking_expire_date);
    }

    /**
     * @param $error
     * @return bool
     */
    public function isTrackingError($error) {
        return !in_array($error, CodeAuthUserTrackingLog::$untracking_errors);
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalTrackingLogByCpActionId($cp_action_id) {
        if (!$cp_action_id) {
            return;
        }
        $tracking_logs = $this->code_auth_track_logs->find(array("cp_action_id" => $cp_action_id));
        if (!$tracking_logs) {
            return;
        }

        foreach ($tracking_logs as $tracking_log) {
            if ($tracking_log) {
                $this->code_auth_track_logs->deletePhysical($tracking_log);
            }
        }
    }

    /**
     * @param $cp_action_id
     * @param $user_id
     */
    public function deletePhysicalTrackingLogByCpActionIdAndUserId($cp_action_id, $user_id) {
        if (!$cp_action_id || !$user_id) {
            return;
        }
        $tracking_logs = $this->code_auth_track_logs->find(array("cp_action_id" => $cp_action_id, "user_id" => $user_id));
        if (!$tracking_logs) {
            return;
        }

        foreach ($tracking_logs as $tracking_log) {
            if ($tracking_log) {
                $this->code_auth_track_logs->deletePhysical($tracking_log);
            }
        }
    }
}