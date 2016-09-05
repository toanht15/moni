<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');

class api_pre_execute_twitter_follow_action extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_execute_twitter_follow_action';

    public $NeedUserLogin = true;
    public $CsrfProtect = true;
    protected $AllowContent = array('JSON');

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

    function doAction() {
        // アカウント連携する
        /** @var CpTwitterFollowLogService $follow_log_service */
        $follow_log_service = $this->getService('CpTwitterFollowLogService');

        $connecting_log = $follow_log_service->getConnectingLogByCpUserIdAndActionId(
            $this->cp_user_id,
            $this->concrete_action_id
        );

        if (!$connecting_log) {
            $follow_log_service->create(
                $this->cp_user_id,
                $this->concrete_action_id,
                CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING
            );
        }

        if($this->is_update_token){
            $this->deleteTwitterAccountSession();
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function deleteTwitterAccountSession(){
        $user_info = $this->getSession('pl_monipla_userInfo');

        if($user_info){

            foreach ($user_info['socialAccounts'] as $key => $social_account) {
                if ($social_account->socialMediaType == CpTwitterFollowActionManager::SOCIAL_TYPE_STRING) {
                    unset($user_info['socialAccounts'][$key]);
                    break;
                }
            }
        }

        $this->setSession('pl_monipla_userInfo', $user_info);
    }
}
