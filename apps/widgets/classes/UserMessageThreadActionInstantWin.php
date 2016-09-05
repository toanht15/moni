<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.instant_win.DrawInstantWinRateStrategy');
AAFW::import('jp.aainc.classes.services.instant_win.DrawInstantWinTimeStrategy');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinPrizeService');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinUserService');
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');
AAFW::import('jp.aainc.classes.services.CpFlowService');
AAFW::import('jp.aainc.classes.services.MoniplaPRService');
AAFW::import('jp.aainc.classes.services.BrandService');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');

class UserMessageThreadActionInstantWin extends aafwWidgetBase {

    const DRAW_VIEW         = 1; // 抽選画面を表示、スピードくじが回せる
    const DRAW_FINISH_VIEW  = 2; // 抽選画面を表示、キャンペーンが終了していて抽選ボタンが無い
    const DRAW_LIMIT_VIEW   = 3; // 抽選画面を表示、当選者が上限数に達していて抽選ボタンが無い
    const PASS_VIEW         = 4; // 当選画面を表示、次に進むボタンを表示
    const STAY_WAITING_VIEW = 5; // 落選画面を表示、再度抽選する権利が残されているので次回参加までのカウントダウンを表示
    const STAY_FINISH_VIEW  = 6; // 落選画面を表示、再度抽選する権利が残されていない

    public function doService($params) {

        $cp_instant_win_action_manager = new CpInstantWinActionManager();
        $instant_win_prize_service = new InstantWinPrizeService();
        $instant_win_user_service = new InstantWinUserService();
        $syn_instant_win_service = new SynInstantWinService();

        $concrete_action = $cp_instant_win_action_manager->getCpConcreteActionByCpActionId($params['message_info']['cp_action']->id);

        /** @var Cp $cp */
        $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);
        $synCp = $cp->getSynCp();
        $params['isForSyndotOnly'] = $cp->isForSyndotOnly();

        $instant_win_user = $instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($params['message_info']['cp_action']->id, $params['cp_user']->id);

        $remain_waiting_time = false;
        $last_join_at = $concrete_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_DAY ? date('Y/m/d 00:00:00', strtotime($instant_win_user->last_join_at)) : $instant_win_user->last_join_at;
        if ($concrete_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $cp_instant_win_action_manager->changeValueIntoTime($concrete_action);
            $remain_waiting_time = strtotime($waiting_time, strtotime($last_join_at)) > time();
            $params['next_time'] = date('Y/m/d H:i:s', strtotime($waiting_time, strtotime($last_join_at)));
        }

        if ($concrete_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $cp_instant_win_action_manager->changeValueIntoTime($concrete_action);
            $has_draw_chance = strtotime($waiting_time, strtotime($last_join_at)) < strtotime($cp->end_date);
        } else {
            $has_draw_chance = false;
        }

        $params['view_number'] = self::STAY_FINISH_VIEW;
        $drawableSecondChallengeLog = null;
        if ($instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
            $params['view_number'] = self::PASS_VIEW;
        } else {
            $status = RequestuserInfoContainer::getInstance()->getStatusByCp($cp);
            $drawableSecondChallengeLog = $syn_instant_win_service->findDrawableSecondChallengeLog($params['cp_user']->user_id,$synCp->id);
            if (($status != Cp::CAMPAIGN_STATUS_DEMO && $status != Cp::CAMPAIGN_STATUS_OPEN) || $cp->isOverTime()) {
                $params['view_number'] = self::DRAW_FINISH_VIEW;
            } elseif ($cp->isOverLimitWinner()) {
                $params['view_number'] = self::DRAW_LIMIT_VIEW;
            } elseif ((!$remain_waiting_time && $concrete_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) ||
                !$instant_win_user->join_count || $drawableSecondChallengeLog) {
                $params['view_number'] = self::DRAW_VIEW;
            } elseif ($has_draw_chance) {
                $params['view_number'] = self::STAY_WAITING_VIEW;
            }
        }

        if($params['isForSyndotOnly']){
            $emptySecondChallengeLog = $syn_instant_win_service->findEmptySecondChallengeLog($params['cp_user']->user_id,$synCp->id);
            $params['hasEmptySecondChallenge'] = $emptySecondChallengeLog ? true : false;
            $params['isDoubleUpChallenge'] = $syn_instant_win_service->isDoubleUpChallenge($params['cp_user']->user_id,$synCp->id);
            $params['isVisibleNextButton'] = Util::isSmartPhone();
        }
        $params['text'] = $concrete_action->text;
        $params['html_content'] = $concrete_action->html_content;
        $params['instant_win_prize_lose'] = $instant_win_prize_service->getInstantWinPrizeByPrizeStatus($concrete_action->id, InstantWinPrizes::PRIZE_STATUS_STAY);
        $params['instant_win_prize_win'] = $instant_win_prize_service->getInstantWinPrizeByPrizeStatus($concrete_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);

        return $params;
    }

}
