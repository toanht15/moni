<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class InstantWinUserService extends aafwServiceBase {

    /** @var InstantWinUsers $instant_win_users */
    protected $instant_win_users;

    /** @var InstantWinUserLogs $instant_win_user_logs */
    protected $instant_win_user_logs;

    public function __construct() {
        $this->instant_win_users = $this->getModel('InstantWinUsers');
        $this->instant_win_user_logs = $this->getModel('InstantWinUserLogs');
    }

    public function createInstantWinUser($cp_action_id, $cp_user_id, $result) {
        $instant_win_user = $this->instant_win_users->createEmptyObject();
        $instant_win_user->cp_action_id = $cp_action_id;
        $instant_win_user->cp_user_id = $cp_user_id;
        $instant_win_user->instant_win_prize_id = $result['instant_win_prize_id'];
        $instant_win_user->prize_status = $result['prize_status'];
        $instant_win_user->last_join_at = date('Y-m-d H:i:s');
        $instant_win_user->join_count = 1;
        return $this->instant_win_users->save($instant_win_user);
    }

    public function updateInstantWinUser($cp_action_id, $cp_user_id, $result) {
        $instant_win_user = $this->getInstantWinUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id);
        $instant_win_user->instant_win_prize_id = $result['instant_win_prize_id'];
        $instant_win_user->prize_status = $result['prize_status'];
        $instant_win_user->last_join_at = date('Y-m-d H:i:s');
        $instant_win_user->join_count = $result['join_count'] + 1;
        $this->instant_win_users->save($instant_win_user);
    }

    public function createInstantWinUserLog($cp_action_id, $cp_user_id, $result) {
        $instant_win_user_log = $this->instant_win_user_logs->createEmptyObject();
        $instant_win_user_log->cp_action_id = $cp_action_id;
        $instant_win_user_log->cp_user_id = $cp_user_id;
        $instant_win_user_log->instant_win_prize_id = $result['instant_win_prize_id'];
        $instant_win_user_log->prize_status = $result['prize_status'];
        $instant_win_user_log->device_type = $result['device_type'];
        $instant_win_user_log->last_join_at = date('Y-m-d H:i:s');
        return $this->instant_win_user_logs->save($instant_win_user_log);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_id
     * @return aafwEntityContainer|array
     */
    public function getInstantWinUserByCpActionIdAndCpUserId($cp_action_id, $cp_user_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_id
        );
        return $this->instant_win_users->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @param $cp_user_ids
     * @return aafwEntityContainer|array
     */
    public function getInstantWinUsersByCpActionIdAndCpUserIds($cp_action_id, $cp_user_ids) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'cp_user_id' => $cp_user_ids
        );

        $instant_win_users = $this->instant_win_users->find($filter);

        if (!$instant_win_users) return;

        $instant_win_user_array = array();
        foreach ($instant_win_users as $instant_win_user) {
            if ($instant_win_user->prize_status) {
                $instant_win_user_array[$instant_win_user->cp_user_id]['prize_status'] = $instant_win_user->prize_status == InstantWinUsers::PRIZE_STATUS_WIN ? '当選' : '落選';
            } else {
                $instant_win_user_array[$instant_win_user->cp_user_id]['prize_status'] = '';
            }
            $instant_win_user_array[$instant_win_user->cp_user_id]['join_count'] = $instant_win_user->join_count;
        }
        return $instant_win_user_array;
    }

    public function countWinnerByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'prize_status' => InstantWinUsers::PRIZE_STATUS_WIN
        );

        return $this->instant_win_users->count($filter);
    }

    /**
     * @param $cp_action_id
     */
    public function deletePhysicalUserLogsByCpActionId ($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $instant_win_users = $this->instant_win_users->find(array('cp_action_id' => $cp_action_id));

        $instant_win_prize_store = $this->getModel("InstantWinPrizes");

        if ($instant_win_users) {
            foreach ($instant_win_users as $instant_win_user) {
                if ($instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
                    $instant_win_prize = $instant_win_prize_store->findOne($instant_win_user->instant_win_prize_id);
                    $instant_win_prize->winner_count -= 1;
                    $instant_win_prize_store->save($instant_win_prize);
                }
                $this->instant_win_users->deletePhysical($instant_win_user);
            }
        }

        $instant_win_user_logs = $this->instant_win_user_logs->find(array('cp_action_id' => $cp_action_id));
        if ($instant_win_user_logs) {
            foreach ($instant_win_user_logs as $instant_win_user_log) {
                $this->instant_win_user_logs->deletePhysical($instant_win_user_log);
            }
        }
    }

    public function deletePhysicalUserLogsByCpActionIdAndCpUserId ($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $instant_win_users = $this->instant_win_users->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));

        $instant_win_prize_store = $this->getModel("InstantWinPrizes");

        if ($instant_win_users) {
            foreach ($instant_win_users as $instant_win_user) {
                if ($instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
                    $instant_win_prize = $instant_win_prize_store->findOne($instant_win_user->instant_win_prize_id);
                    $instant_win_prize->winner_count -= 1;
                    $instant_win_prize_store->save($instant_win_prize);
                }
                $this->instant_win_users->deletePhysical($instant_win_user);
            }
        }

        $instant_win_user_logs = $this->instant_win_user_logs->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if ($instant_win_user_logs) {
            foreach ($instant_win_user_logs as $instant_win_user_log) {
                $this->instant_win_user_logs->deletePhysical($instant_win_user_log);
            }
        }
    }

}
