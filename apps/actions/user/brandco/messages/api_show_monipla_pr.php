<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

AAFW::import('jp.aainc.classes.services.instant_win.InstantWinUserService');
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');

AAFW::import('jp.aainc.classes.services.monipla_pr.MoniplaLottery');
AAFW::import('jp.aainc.classes.services.monipla_pr.MoniplaRecommendCp');
AAFW::import('jp.aainc.classes.services.monipla_pr.MoniplaMedia');

/**
 * スレッドに適切なPRテンプレートを返す
 * 最後のモジュール表示・スピードくじ落選時に呼び出し
 * Class api_show_monipla_pr
 */
class api_show_monipla_pr extends BrandcoGETActionBase {

    protected $ContainerName = 'api_show_monipla_pr';
    protected $AllowContent = array('JSON');
    public $NeedOption = array(BrandOptions::OPTION_CP);

    protected $brand;
    protected $cp;
    protected $user;
    protected $cp_user;

    /** @var CpFlowService $cp_flow_service */
    protected $cp_flow_service;
    /** @var UserService $user_service */
    protected $user_service;
    /** @var CpUserService $cp_user_service */
    protected $cp_user_service;

    public function doThisFirst() {
        $this->cp_flow_service = $this->createService('CpFlowService');
        $this->user_service = $this->createService('UserService');
        $this->cp_user_service = $this->createService('CpUserService');
    }

    public function validate() {
        return true;
    }

    function doAction() {
        $this->brand = $this->getBrand();
        $this->cp = $this->cp_flow_service->getCpById($this->GET['cp_id']);
        $this->user = $this->user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);
        $this->cp_user = $this->cp_user_service->getCpUserByCpIdAndUserId($this->cp->id, $this->user->id);

        if (!$this->canDisplay()) {
            $json_data = $this->createAjaxResponse('ng', array(), array('content' => 'Must not display'));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $html = $this->selectPRTemplateHtml();
        if (!$html) {
            $json_data = $this->createAjaxResponse('ng', array(), array('content' => 'Not found template'));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function canDisplay() {
        if (!$this->isCampaign()) {
            return false;
        }

        if ($this->isClosedCp()) {
            return false;
        }

        if ($this->isWhiteListBrand()) {
            return false;
        }

        if ($this->cp->isSynCp()) {
            return false;
        }

        $cp_action = $this->cp_flow_service->searchInstantWinCpActionByCpId($this->cp->id);
        if ($cp_action) {
            if (!$this->isLosingInstantWin($cp_action)) return false;
        } else {
            if (!$this->readLastAction()) return false;
        }

        return true;
    }

    private function isCampaign() {
        return $this->cp->type == Cp::TYPE_CAMPAIGN;
    }

    private function isClosedCp() {
        return $this->cp->join_limit_flg == Cp::JOIN_LIMIT_ON;
    }

    private function isWhiteListBrand() {
        return $this->brand->monipla_pr_allow_type == Brand::MONIPLA_PR_ALLOW_TYPE_DISALLOWED;
    }

    private function readLastAction() {
        $last_action = $this->cp_flow_service->getLastActionOfFirstGroupByCpId($this->cp->id);
        return $this->cp_user_service->getCpUserActionMessagesByCpUserIdAndCpActionId($this->cp_user->id, $last_action->id);
    }

    private function isLosingInstantWin($cp_action) {
        $instant_win_user_service = new InstantWinUserService();
        $instant_win_user = $instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($cp_action->id, $this->cp_user->id);

        //PASS_VIEW
        if ($instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) return false;

        //DRAW_LIMIT_VIEW || DRAW_FINISH_VIEW
        if ($this->cp->isOverLimitWinner() || $this->cp->isOverTime()) return true;

        //DRAW_VIEW
        if (!$instant_win_user->join_count) return false;
        AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
        $cp_instant_win_action_manager = new CpInstantWinActionManager();
        $cp_instatnt_win_action = $cp_instant_win_action_manager->getCpConcreteActionByCpActionId($cp_action->id);
        if ($cp_instatnt_win_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $last_join_at = $cp_instatnt_win_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_DAY ? date('Y/m/d 00:00:00', strtotime($instant_win_user->last_join_at)) : $instant_win_user->last_join_at;
            $waiting_time = $cp_instant_win_action_manager->changeValueIntoTime($cp_instatnt_win_action);
            $remain_waiting_time = strtotime($waiting_time, strtotime($last_join_at)) > time();
            if (!$remain_waiting_time) return false;
        }
        
        return true;
    }

    private function selectPRTemplateHtml() {
        // 優先度の高いものから順に追加して下さい
        $template_classes = [
            new MoniplaLottery(),
            new MoniplaRecommendCp(),
            new MoniplaMedia(),
        ];

        foreach ($template_classes as $template_class) {
            if ($template_class->isMine($this->cp, $this->cp_user)) {
                return $template_class->parseTemplate($this->cp, $this->cp_user);
            }
        }
        
        return null;
    }
}
