<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_instagram_hashtag_check_status extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_instagram_hashtag_check_status';

    public function validate() {

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse('ng', array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    /**
     * override
     * @return string
     */
    public function doAction(){
        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function saveData(){}
}
