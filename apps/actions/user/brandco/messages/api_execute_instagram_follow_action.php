<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstagramFollowActionManager');
AAFW::import('jp.aainc.vendor.instagram.Instagram');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_execute_instagram_follow_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_instagram_follow_action';

    public function validate() {

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
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

    }
}
