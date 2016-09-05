<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.vendor.twitter.Twitter');

class CpRetweetMessageService extends aafwServiceBase {

    private $config;
    private $logger;
    private $twitter;

    public function __construct($twOAuthToken = null, $twOAuthTokenSecret = null) {
        $this->config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        if ($twOAuthToken && $twOAuthTokenSecret) {
            $this->twitter = new Twitter(
                $this->config->query('@twitter.User.ConsumerKey'),
                $this->config->query('@twitter.User.ConsumerSecret'),
                $twOAuthToken,
                $twOAuthTokenSecret
            );
        } else {
            $this->twitter = new Twitter(
                $this->config->query('@twitter.Admin.ConsumerKey'),
                $this->config->query('@twitter.Admin.ConsumerSecret'),
                $this->config->query('@twitter.Admin.AccessToken'),
                $this->config->query('@twitter.Admin.AccessTokenSecret')
            );
        }
    }

    public function postRetweet($tweet_id) {
        $res = $this->twitter->postRetweet($tweet_id);
        if ($res) {
            return 'success';
        } else {
            return 'api_error';
        }
    }

    public function getTweetContentByTweetId($tweet_id) {
        $results = array();
        $result = $this->twitter->getTweetContent($tweet_id);
        if (!$result) return null;
        $results['tweet_id']                    = $tweet_id;
        $results['tweet_text']                  = $this->getTweetText($result);
        $results['tweet_date']                  = date('Y-m-d H:i:s', strtotime($result->created_at));
        $results['twitter_name']                = $result->user->name;
        $results['twitter_screen_name']         = $result->user->screen_name;
        $results['twitter_profile_image_url']   = $result->user->profile_image_url;
        if ($result->extended_entities->media) {
            $results['tweet_has_photo']         = 1;
            $results['tweet_photos']            = array();
            foreach ($result->extended_entities->media as $element) {
                $results['tweet_photos'][]      = $element->media_url;
            }
        }
        return $results;
    }

    private function getTweetText($result) {
        $text = $result->text;
        if ($result->entities->urls) {
            foreach ($result->entities->urls as $element) {
                $text = str_replace($element->url, $element->expanded_url, $text);
            }
        }
        if ($result->entities->media) {
            $media = $result->entities->media[0];
            $text = str_replace($media->url, '', $text);
        }
        return $text;
    }

    public function getTweetIdByTweetUrl($tweet_url) {
        if (!$tweet_url) return false;
        $isValid = preg_match('/^((http|https)(:\/\/))*(www\.)*(twitter\.com\/)([a-zA-Z0-9_]{1,15})(\/status\/)([0-9]+)$/', $tweet_url, $matches);
        if ($isValid) {
            return array_pop($matches);
        }
        return false;
    }
}