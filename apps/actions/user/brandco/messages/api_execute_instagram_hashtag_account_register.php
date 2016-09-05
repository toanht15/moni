<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_execute_instagram_hashtag_account_register extends ExecuteActionBase {

    protected $ContainerName = 'api_execute_instagram_hashtag_account_register';
    public $NeedOption = array();

    public function validate() {
        $errors = array();

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
        }

        if (!$this->POST['instagram_user_name']) {
            $errors['instagram_user_name'] = '必ず入力してください';
        } elseif (!$this->isAlnumSymbol($this->POST['instagram_user_name'])) {
            $errors['instagram_user_name'] = '半角英数字記号のみ入力可能です';
        } elseif (preg_match('/#|@|-/', $this->POST['instagram_user_name'])) {
            $errors['instagram_user_name'] = '入力文字に不正な記号が含まれています';
        }

        if (count($errors)) {
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

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->createService('CpUserService');

        /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
        $instagram_hashtag_user_service = $this->createService('InstagramHashtagUserService');

        $cp_action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);

        $manager = $cp_action->getActionManagerClass();

        $concrete_action = $manager->getConcreteAction($cp_action);

        $message = $cp_user_service->getCpUserActionMessagesByCpUserIdAndCpActionId($this->cp_user_id, $cp_action->id);

        $action_status = $cp_user_service->getCpUserActionStatusByCpUserIdAndCpActionId($this->cp_user_id, $cp_action->id);

        // ユーザ参加情報チェック
        $instagram_hashtag_user = $instagram_hashtag_user_service->getInstagramHashtagUserByCpActionIdAndCpUserId($cp_action->id, $this->cp_user_id);

        if (!$instagram_hashtag_user) {
            $instagram_hashtag_user = $instagram_hashtag_user_service->createEmptyObject();
            $instagram_hashtag_user->instagram_user_name = $this->POST['instagram_user_name'];
            $instagram_hashtag_user->cp_action_id = $cp_action->id;
            $instagram_hashtag_user->cp_user_id = $this->cp_user_id;
            $instagram_hashtag_user_service->saveInstagramHashtagUser($instagram_hashtag_user);
        }

        $message_info = array(
            "cp_action" => $cp_action,
            "concrete_action" => $concrete_action,
            "message" => $message,
            "action_status" => $action_status
        );

        $cp_user = $cp_user_service->getCpUserById($this->cp_user_id);

        $cp = $cp_flow_service->getCpById($cp_user->cp_id);

        $cp_status = RequestuserInfoContainer::getInstance()->getStatusByCp($cp);
        $cp_info = $cp_flow_service->getCampaignInfo($cp,$this->brand, null, $cp_status);

        // HTMLを作成
        $parser = new PHPParser();
        $html = $parser->parseTemplate(
            'CpMessageAction.php',
            array(
                'cp_user' => $cp_user,
                'message_info' => $message_info,
                'pageStatus' => $this->Data['pageStatus'],
                'cp_info' => $cp_info
            )
        );

        $data = array(
            'message_id'  => $message->id
        );

        $json_data = $this->createAjaxResponse("ok", $data, array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function saveData() {}
}
