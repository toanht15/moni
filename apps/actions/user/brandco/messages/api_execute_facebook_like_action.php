<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.services.CpFacebookLikeLogService');

class api_execute_facebook_like_action extends ExecuteActionBase {

    protected $ContainerName = 'api_execute_engagement_action';
    protected $brand_social_account;

    public function validate() {
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
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

    function saveData() {
        $engagement_log_service = $this->getService('CpFacebookLikeLogService');
        $engagement_log_service->create($this->POST);
    }
}
