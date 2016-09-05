<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

abstract class CpTweetManagerActionBase extends BrandcoPOSTActionBase {
    protected $ContainerName = 'tweet_posts';

    protected $tweet_approval_status;

    protected $logger;
    protected $tweet_service;
    protected $tweet_transaction;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->tweet_service = $this->getService('TweetMessageService');
        $this->tweet_transaction = aafwEntityStoreFactory::create('TweetMessages');
    }

    public function validate() {
        return true;
    }

    public function updateTweetCampaign($tweet_message_id) {
        try {
            $this->tweet_transaction->begin();

            $tweet_message = $this->tweet_service->getTweetMessageById($tweet_message_id);

            if ($tweet_message->tweet_status == TweetMessage::TWEET_STATUS_PUBLIC && $tweet_message->approval_status != $this->tweet_approval_status) {
                $tweet_message->approval_status = $this->tweet_approval_status;
                $this->tweet_service->saveTweetMessageData($tweet_message);
            }

            $this->tweet_transaction->commit();
        } catch (Exception $e) {
            $this->tweet_transaction->rollback();
            throw $e;
        }
    }
}