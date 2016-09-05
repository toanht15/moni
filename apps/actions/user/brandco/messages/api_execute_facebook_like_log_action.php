<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserSNSActionValidator');
AAFW::import('jp.aainc.classes.services.CpFacebookLikeLogService');

class api_execute_facebook_like_log_action extends ExecuteActionBase {

    protected $ContainerName = 'api_execute_fb_like_action';

    public function validate() {
        $log_check = false;
        $validator = new UserSNSActionValidator(
            $this->cp_user_id,
            $this->cp_action_id,
            $this->brand_social_account_id,
            $log_check
        );
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
        $engagement_log_service = $this->getService('CpFacebookLikeLogService');
        $engagement_log_service->create($this->POST);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    function saveData() {}
}
