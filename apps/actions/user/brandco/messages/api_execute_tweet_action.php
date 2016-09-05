<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_tweet_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_tweet_action';
    protected $tweet_message_service;
    protected $cp_tweet_action_service;
    protected $cp_tweet_action;

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

        $this->cp_tweet_action_service = $this->createService('CpTweetActionService');
        $this->cp_tweet_action = $this->cp_tweet_action_service->getCpTweetAction($this->cp_action_id);

        $this->tweet_message_service = $this->createService('TweetMessageService');
        $tweet_message = $this->tweet_message_service->getTweetMessageByCpUserId($this->cp_user_id, $this->cp_tweet_action->id);
        if ($tweet_message->tweet_content_url == '' && !$this->skipped) {
            $errors['tweet_error'] = 'エラーが発生しました。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }
    function saveData() {
        if ($this->skipped) {
            $this->tweet_message_service->updateTweetMessage(array('cp_user_id'=>$this->cp_user_id, 'cp_tweet_action_id'=>$this->cp_tweet_action->id, 'skipped'=>$this->skipped));
        }
    }
}