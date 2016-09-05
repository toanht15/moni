<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.services.GiftMessageService');
AAFW::import('jp.aainc.classes.services.CpGiftActionService');

class api_execute_gift_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_gift_action';

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

        /** @var CpGiftActionService $cp_gift_action_service */
        $cp_gift_action_service = $this->getService('CpGiftActionService');
        $cp_gift_action   = $cp_gift_action_service->getCpGiftAction($this->cp_action_id);

        /** @var GiftMessageService $gift_message_service */
        $gift_message_service = $this->getService('GiftMessageService');
        $gift_message_service->updateGreetingCardSendStatus($this->cp_user_id, $cp_gift_action->id, GiftMessage::SENT, $this->media_type);
    }
}
