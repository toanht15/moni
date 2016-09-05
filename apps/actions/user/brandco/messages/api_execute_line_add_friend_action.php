<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_line_add_friend_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_line_add_friend_action';

    private $line_add_friend_action_log_service;

    public function doThisFirst() {
        /** @var CpLineAddFriendActionLogService $line_add_friend_action_log_service */
        $this->line_add_friend_action_log_service = $this->getService('CpLineAddFriendActionLogService');
    }

    public function validate() {

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        if($this->isClickAddFriendUrl() && $this->isExistLineAddFriendActionLog()) {
            $json_data = $this->createAjaxResponse("ng", array(), array());
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }
    function saveData() {

        if($this->isClickAddFriendUrl()) {
            $this->line_add_friend_action_log_service->createLog($this->cp_line_add_friend_action_id, $this->cp_user_id);
        }
    }

    private function isClickAddFriendUrl() {
        return $this->cp_line_add_friend_action_id !== '';
    }

    private function isExistLineAddFriendActionLog() {
        $line_add_friend_action_log = $this->line_add_friend_action_log_service->findLogByCpActionIdAndCpUserId($this->cp_line_add_friend_action_id, $this->cp_user_id);
        return $line_add_friend_action_log ? true : false;
    }
}
