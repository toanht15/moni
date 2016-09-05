<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.vendor.twitter.Twitter');
AAFW::import('jp.aainc.classes.entities.CpShareUserLog');

class api_execute_share_action extends ExecuteActionBase {

    protected $ContainerName = 'api_execute_share_action';
    private $cp_user;
    protected $Form = array(
        'package' => 'message',
        'action' => 'thread/{cp_action_id}',
    );

    public function doThisFirst() {

        /** @var CpUserService $cp_user_service */
        /** @var CpFlowService $cp_flow_service */
        $cp_user_service = $this->createService('CpUserService');
        $this->cp_user = $cp_user_service->getCpUserById($this->cp_user_id);

    }

    public function validate() {
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse('ng', array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        $validatorDefinition = array();

        $validator = new aafwValidator($validatorDefinition);
        $validator->validate($this->POST);

        if($validator->getErrorCount()) {
            $errorMessages['message'] = $validator->getMessage('message');
            $json_data = $this->createAjaxResponse("ng", array(), $errorMessages);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function saveData() {

        $share_action_manager = new CpShareActionManager();
        $share_action = $share_action_manager->getCpShareActionByCpActionId($this->cp_action_id);

        /** @var CpShareUserLogService $cp_share_user_log_service */
        $cp_share_user_log_service = $this->getService('CpShareUserLogService');

        if ($this->unread_flg) {
            //未連携でモジュールが表示されてない
            $cp_share_user_log_service->createOrUpdate($this->cp_user_id, $share_action->id, CpShareUserLog::TYPE_UNREAD);
        } elseif ($this->skip_flg) {
            //スキップが押された
            $cp_share_user_log_service->createOrUpdate($this->cp_user_id, $share_action->id, CpShareUserLog::TYPE_SKIP);
        } else {
            //シェアボタン押された
            $cp = CpInfoContainer::getInstance()->getCpById($this->cp_user->cp_id);
            $link = $share_action->share_url ? $share_action->share_url : $cp->getReferenceUrl();
            $user_sns_account_manager = new UserSnsAccountManager($this->Data['pageStatus']['userInfo'], null, $this->Data['pageStatus']['brand']->app_id);
            $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($this->getSNSAccountId($this->Data['pageStatus']['userInfo'], 'Facebook'), 'Facebook');

            if($sns_account_info){

                if ($this->isCanPostSNS()) {
                    $this->getFacebookUser()->setToken($sns_account_info['social_media_access_token']);
                    $this->getFacebookUser()->postShare($this->message, $link);
                }

                $cp_share_user_log_service->createOrUpdate($this->cp_user_id, $share_action->id, CpShareUserLog::TYPE_SHARE, $this->message);
            }else{
                $cp_share_user_log_service->createOrUpdate($this->cp_user_id, $share_action->id, CpShareUserLog::TYPE_ERROR, $this->message);
            }
        }
    }
}
