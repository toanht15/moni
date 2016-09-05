<?php


AAFW::import ('jp.aainc.aafw.classes.services.instant_win.InstantWinPrizeService');
AAFW::import ('jp.aainc.aafw.classes.services.instant_win.InstantWinUserService');
AAFW::import ('jp.aainc.actions.user.brandco.messages.api_pre_execute_draw_instant_win_action');

class api_pre_execute_draw_instant_win_actionTest extends BaseTest {

    function testRemainWaitingTime_whenNotWait() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));

        $manager = new CpInstantWinActionManager();
        list($new_cp_action, $concrete_cp_action) = $manager->createCpActions($cp_action_group->id, CpAction::TYPE_INSTANT_WIN, 0, 1);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_LOTTERY;
        $this->save('Cps', $cp);

        $prize_service = new InstantWinPrizeService();
        $prize = $prize_service->getInstantWinPrizeByPrizeStatus($concrete_cp_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $prize->winner_count = 0;
        $this->save('InstantWinPrizes', $prize);

        $target = new api_pre_execute_draw_instant_win_action();
        $target->cp_action_id = $cp_action->id;
        $target->beforeValidate();

        $this->assertFalse($target->remainWaitingTime());
    }

    function testRemainWaitingTime_whenWait() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));

        $manager = new CpInstantWinActionManager();
        list($new_cp_action, $concrete_cp_action) = $manager->createCpActions($cp_action_group->id, CpAction::TYPE_INSTANT_WIN, 0, 1);

        $concrete_cp_action->once_flg = InstantWinPrizes::ONCE_FLG_OFF;
        $concrete_cp_action->time_value = 999;
        $this->save('CpInstantWinActions', $concrete_cp_action);

        $cp->winner_count = 1;
        $cp->selection_method = CpCreator::ANNOUNCE_LOTTERY;
        $this->save('Cps', $cp);

        $prize_service = new InstantWinPrizeService();
        $prize = $prize_service->getInstantWinPrizeByPrizeStatus($concrete_cp_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
        $prize->winner_count = 0;
        $this->save('InstantWinPrizes', $prize);

        $user_service = new InstantWinUserService();
        $prize_user = $user_service->createInstantWinUser($new_cp_action->id, $cp_user->id, array(
            'instant_win_prize_id' => $prize->id,
            'prize_status' => InstantWinUsers::PRIZE_STATUS_LOSE
        ));
        $prize_user->last_join_at = '2999-12-31 00:00:00';
        $this->save('InstantWinUsers', $prize_user);

        $target = new api_pre_execute_draw_instant_win_action();
        $target->cp_action_id = $new_cp_action->id;
        $target->cp_user_id = $cp_user->id;
        $target->beforeValidate();

        $this->assertTrue($target->remainWaitingTime());
    }
}