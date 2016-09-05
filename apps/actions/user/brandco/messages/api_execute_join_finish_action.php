<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessageActionValidator');
AAFW::import('jp.aainc.classes.services.monipla.UpdateMoniplaCpInfo');
AAFW::import('jp.aainc.classes.services.monipla.SendCpInfoForMonipla');

class api_execute_join_finish_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_join_finish_action';

    public function validate() {

        $validator = new UserMessageActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    function saveData() {
        if ($this->isCanSendCpInfoForMonipla()) {
            /** @var SendCpInfoForMonipla $send_cp_info_for_monipla */
            $send_cp_info_for_monipla = $this->createService('SendCpInfoForMonipla');
            $send_cp_info_for_monipla->sendCpUserStatus($this->cp_user_id, $this->cp_action_id, $this->brand->app_id, CpAction::TYPE_JOIN_FINISH);

            /** @var UpdateMoniplaCpInfo $update_monipla_cp_info */
            $update_monipla_cp_info = $this->createService('UpdateMoniplaCpInfo');
            $update_monipla_cp_info->sendCpUserStatus($this->cp_user_id, $this->cp_action_id, $this->brand->app_id, CpAction::TYPE_JOIN_FINISH);
        }
    }
}
