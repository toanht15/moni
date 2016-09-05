<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserSNSActionValidator');
AAFW::import('jp.aainc.classes.services.EngagementLogService');

class api_execute_fb_like_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_fb_like_action';

    public function validate() {
        $validator = new UserSNSActionValidator($this->cp_user_id, $this->cp_action_id, $this->brand_social_account_id);
        $validator->validate();

        // 共通validate
        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    public function doAction() {
        $engagement_log_service = $this->getService('EngagementLogService');
        $engagement_log_service->create($this->POST);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function saveData() {}
}
