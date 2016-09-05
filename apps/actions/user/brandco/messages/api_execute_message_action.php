<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessageActionValidator');

class api_execute_message_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_message_action';

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
        // pass
    }
}
