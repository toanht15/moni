<?php
AAFW::import('jp.aainc.classes.services.monipla_pr.IMoniplaPR');

class MoniplaRecommendCp implements IMoniplaPR {

    const CP_ID_BEGINNER = 8628;        //初心者限定cp_id
    const CP_ID_MONIPLA  = 9469;        //事務局cp_id

    public function isMine(Cp $cp, CpUser $cp_user) {
        /** @var UserService $user_service */
        $user_service = new UserService();
        $user = $user_service->getUserByBrandcoUserId($cp_user->user_id);
        $beginner_term = date("Y-m-d H:i:s", strtotime("-30 day"));

        //条件：cp同じでない　&&　cp参加していない　&&　cp開催中　&&　初心者である
        if ($user->created_at >= $beginner_term) {
            if ($this->hasRecommendCp($cp, $cp_user, self::CP_ID_BEGINNER)) return true;
        }
        return $this->hasRecommendCp($cp, $cp_user, self::CP_ID_MONIPLA);
    }

    public function parseTemplate(Cp $cp, CpUser $cp_user)
    {
        //createService
        /** @var UserService $user_service */
        $user_service = new UserService();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = new CpFlowService();

        $user = $user_service->getUserByBrandcoUserId($cp_user->user_id);
        $beginner_term = date("Y-m-d H:i:s", strtotime("-30 day"));

        $recommend_cp_ga_tag = null;
        $recommend_cp = null;
        if ($user->created_at >= $beginner_term && $this->hasRecommendCp($cp, $cp_user, self::CP_ID_BEGINNER)) {
            $recommend_cp_beginner = $cp_flow_service->getCpById(self::CP_ID_BEGINNER);
            $recommend_cp = $recommend_cp_beginner;
            $recommend_cp_ga_tag = 'show-beginner-cp';
        } else if ($this->hasRecommendCp($cp, $cp_user, self::CP_ID_MONIPLA)) {
            $recommend_cp_monipla = $cp_flow_service->getCpById(self::CP_ID_MONIPLA);
            $recommend_cp = $recommend_cp_monipla;
            $recommend_cp_ga_tag = 'show-monipla-cp';
        }

        $parser = new PHPParser();
        return $parser->parseTemplate(
            'UserMessageThreadMoniplaRecommendCp.php',
            array(
                'cp' => $cp,
                'cp_user' => $cp_user,
                'recommend_cp' => $recommend_cp,
                'recommend_cp_ga_tag' => $recommend_cp_ga_tag
            )
        );
    }

    public function hasRecommendCp(Cp $cp, CpUser $cp_user, $recommend_cp_id)
    {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = new CpFlowService();

        $recommend_cp = $cp_flow_service->getCpById($recommend_cp_id);
        if ($recommend_cp->id === $cp->id) {
            return false;
        }

        if($recommend_cp->status !== Cp::STATUS_FIX) {
            return false;
        }

        $current_date = date("Y-m-d H:i:s");
        if ($recommend_cp->start_date >= $current_date
            || $recommend_cp->end_date <= $current_date) {
            return false;
        }

        /** @var CpUserService $cp_user_service */
        $cp_user_service = new CpUserService();
        $joinCp = $cp_user_service->getCpUserByCpIdAndUserId($recommend_cp_id, $cp_user->user_id);

        if (!is_null($joinCp)) {
            if (!$this->isInstantWinCpOnTime($cp_flow_service->getCpActionIdsByCpIdAndType($recommend_cp_id, CpAction::TYPE_INSTANT_WIN)[0],$joinCp->id)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $cp_action_id
     * @param $user_id
     * @return bool
     */
    public function isInstantWinCpOnTime($cp_action_id, $cp_user_id) {
        $cp_instant_win_action_manager = new CpInstantWinActionManager();
        $concrete_action = $cp_instant_win_action_manager->getCpConcreteActionByCpActionId($cp_action_id);

        $instant_win_user_service = new InstantWinUserService();

        $instant_win_user = $instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($cp_action_id,$cp_user_id);
        $last_join_at = $concrete_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_DAY ? date('Y/m/d 00:00:00', strtotime($instant_win_user->last_join_at)) : $instant_win_user->last_join_at;
        if ($concrete_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $cp_instant_win_action_manager->changeValueIntoTime($concrete_action);
            $remain_waiting_time = strtotime($waiting_time, strtotime($last_join_at)) > time();
            if (!$remain_waiting_time) {
                return true;
            }
        }
        return false;
    }
}