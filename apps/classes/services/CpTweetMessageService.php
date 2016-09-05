<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.vendor.twitter.Twitter');

class CpTweetMessageService extends aafwServiceBase {

    private $config;
    private $logger;
    private $twitter;

    public function __construct($twOAuthToken, $twOAuthTokenSecret) {
        $this->config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->twitter = new Twitter(
            $this->config->query('@twitter.User.ConsumerKey'),
            $this->config->query('@twitter.User.ConsumerSecret'),
            $twOAuthToken,
            $twOAuthTokenSecret
        );
    }

    public function postTweet($status, $image_urls = array()) {
        $media_ids = array();
        //画像アップロードする
        foreach ($image_urls as $image_url) {
            $res = $this->twitter->uploadMedia($image_url);
            if ($res) {
                $upload_res = json_decode($res);
                $media_ids[] = $upload_res->media_id;
            } else {
                return 'api_error';
            }
        }
        $res = $this->twitter->postTweetWithMedia($status, $media_ids);
        if ($res) {
            $post_res = json_decode($res);
            return 'http://twitter.com/' . $post_res->user->screen_name . '/status/' . $post_res->id;
        } else {
            return 'api_error';
        }
    }
}