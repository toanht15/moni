<?php
AAFW::import('jp.aainc.t.testcases.CpBaseTest');

class InstantWinCpTestHelper extends CpBaseTest {

    public function saveAction(CpActions $cp_action, $condition = null) {
        if (!$cp_action) return;

        if ($condition['cp_instant_win_actions']) {
            $cp_concrete_action = $this->updateCpConcreteAction('CpInstantWinActions', $cp_action->id, $condition['cp_instant_win_actions']);
        } else {
            $cp_concrete_action = $this->getCpConcreteAction('CpInstantWinActions', $cp_action->id);
        }
        if (!$cp_concrete_action) return;

        $instant_win_prizes[] = $this->createInstantWinPrize($cp_concrete_action->id, InstantWinPrizes::PRIZE_STATUS_STAY);
        $instant_win_prizes[] = $this->createInstantWinPrize($cp_concrete_action->id, InstantWinPrizes::PRIZE_STATUS_PASS, $condition['instant_win_prizes']);

        return array('cp_concrete_action' => $cp_concrete_action, 'instant_win_prizes' => $instant_win_prizes);
    }

    protected function createInstantWinPrize($cp_concrete_action_id, $prize_status, $condition = null) {
        $winner_count = 0;
        $winning_rate = 0;
        if ($prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
            $winner_count = $condition['winner_count'] ?: 1;
            $winning_rate = $condition['winner_count'] ?: 0.001;
        }

        $instant_win_prize = $this->entity(
            'InstantWinPrizes',
            array(
                'cp_instant_win_action_id' => $cp_concrete_action_id,
                'winner_count' => $winner_count,
                'winning_rate' => $winning_rate,
                'prize_status' => $prize_status
            )
        );
        return $instant_win_prize;
    }


    public function joinAction(CpActions $cp_action, CpUsers $cp_user, $prize_status = InstantWinUsers::PRIZE_STATUS_WIN) {
        if (!$cp_action || !$cp_user) return;

        $cp_concrete_action = $this->findOne('CpInstantWinActions', array('cp_action_id' => $cp_action->id));
        if (!$cp_concrete_action) return;
        $instant_win_prize = $this->findOne('InstantWinPrizes', array('cp_concrete_action' => $cp_concrete_action->id, 'prize_status' => $prize_status));
        if (!$instant_win_prize) return;

        list($instant_win_user, $instant_win_user_log) = $this->drawInstantWin($cp_action->id, $cp_user->id, $instant_win_prize->id, $prize_status);

        $action_status = $prize_status == InstantWinUsers::PRIZE_STATUS_WIN ? null : CpUserActionStatus::NOT_JOIN ;
        $this->updateCpUserActionMessageAndStatus($cp_concrete_action->cp_action_id, $cp_user->id, $action_status);
        return array('instant_win_user' => $instant_win_user, 'instant_win_user_log' => $instant_win_user_log);
    }

    protected function drawInstantWin($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->findOne('InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        if (!$instant_win_user) {
            $instant_win_user = $this->createInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        } else {
            $instant_win_user = $this->updateInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        }

        $instant_win_user_log = $this->createInstantWinUserLog($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status);
        return array($instant_win_user, $instant_win_user_log);
    }

    protected function createInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->entity(
            'InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user;
    }

    protected function updateInstantWinUser($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user = $this->updateEntities(
            'InstantWinUsers',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
            ),
            array(
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user;
    }

    protected function createInstantWinUserLog($cp_action_id, $cp_user_id, $instant_win_prize_id, $prize_status) {
        $instant_win_user_log = $this->entity(
            'InstantWinUserLogs',
            array(
                'cp_action_id' => $cp_action_id,
                'cp_user_id' => $cp_user_id,
                'instant_win_prize_id' => $instant_win_prize_id,
                'prize_status' => $prize_status
            )
        );
        return $instant_win_user_log;
    }
} 