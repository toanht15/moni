<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserInstantWinActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinUserService');

class api_execute_instant_win_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_instant_win_action';

    /** @var InstantWinUserService $instant_win_user_service */
    protected $instant_win_user_service;

    public function beforeValidate () {
        $this->instant_win_user_service = $this->getService('InstantWinUserService');
    }

    public function validate() {

        $validator = new UserInstantWinActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        if (!$this->isWinner()) {
            $errors['cp_action_id'][] = "当選していません";
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function saveData() {

    }

    /**
     * 当選済みか確認
     * @return bool
     */
    public function isWinner() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        return $instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS;
    }
}
