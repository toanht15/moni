<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_apply_tweet_url extends BrandcoGETActionBase {

    protected $AllowContent = array('JSON');
    public $NeedOption = array();
    public $tweet_id;
    public $cp_retweet_message_service;

    public function doThisFirst() {
        $this->cp_retweet_message_service  = $this->createService('CpRetweetMessageService');
    }

    public function validate() {
        $this->tweet_id = $this->cp_retweet_message_service->getTweetIdByTweetUrl($this->tweet_url);
        if (!$this->tweet_id) {
            $errors['tweet_url_error'] = 'ツイートのURLを入力してください。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function doAction() {
        $tweet_content               = $this->cp_retweet_message_service->getTweetContentByTweetId($this->tweet_id);

        $json_data = $this->createAjaxResponse("ok", array('tweet_content' => $tweet_content));
        $this->assign('json_data', $json_data);
        return 'dummy.php';

    }
}