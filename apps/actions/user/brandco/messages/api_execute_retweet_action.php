<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_retweet_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_retweet_action';
    protected $retweet_message_service;
    protected $cp_retweet_action_service;
    protected $cp_retweet_action;

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

        $this->cp_retweet_action_service = $this->createService('CpRetweetActionService');
        $this->cp_retweet_action = $this->cp_retweet_action_service->getCpRetweetAction($this->cp_action_id);

        $this->retweet_message_service = $this->createService('RetweetMessageService');
        $retweet_message = $this->retweet_message_service->getRetweetMessageByCpUserId($this->cp_user_id, $this->cp_retweet_action->id);
        if (!in_array($retweet_message->retweeted, array(CpRetweetAction::POSTED_RETWEET, CpRetweetAction::CONNECT_AND_POSTED_RETWEET)) && !$this->skipped) {
            $errors['retweet_error'] = 'エラーが発生しました。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }
    function saveData() {
        if ($this->skipped) {
            $this->retweet_message_service->updateRetweetMessage(array('cp_user_id'=>$this->cp_user_id, 'cp_retweet_action_id'=>$this->cp_retweet_action->id, 'skipped'=>$this->skipped));
        }
    }
}