<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.batch.GetTwitterDataBatchBase');

class GetExternalTwitterEntries extends GetTwitterDataBatchBase {
    const TWITTER_API_RATE_LIMIT = 300;
    const MAX_TWEET = 200;

    protected $external_tw_stream_service;

    public function __construct() {
        parent::__construct();
        $this->external_tw_stream_service = $this->service_factory->create('ExternalTwStreamService');
    }

    public function executeProcess() {
        if (!$this->initTwitter()) {
            throw new aafwException('Twitter Authenticate failed!');
        }

        $streams = $this->external_tw_stream_service->getAllStreams();

        foreach ($streams as $stream) {
            $this->getTweetsFromStream($stream);
        }
    }

    /**
     * @return bool
     */
    private function initTwitter() {
        //Init Twitter Client
        $consumer_key = config('@twitter.Admin.ConsumerKey');
        $consumer_secret = config('@twitter.Admin.ConsumerSecret');

        $this->twitter = $this->initTwitterClient($consumer_key, $consumer_secret);

        if (!$this->twitter) {
            return false;
        }

        return true;
    }

    /**
     * @param $stream
     */
    private function getTweetsFromStream($stream) {
        /** @var ExternalTwEntryService $external_tw_entry_service */
        $external_tw_entry_service = $this->service_factory->create('ExternalTwEntryService');

        $since_id = Util::isNullOrEmpty($stream->url) ? null : $stream->url;
        $max_id = null;
        $save_url = null;
        $count = 0;

        while (1) {
            $tweets = $this->getTweetsByTwitterId($stream->social_media_account_id, $since_id, $max_id);
            $tweets = $this->removeDuplicateElementById($tweets, $max_id);

            if (!$tweets || count($tweets) == 1) {
                break;
            }

            if (($count == 0)) {
                $save_url = $tweets[0]['id'];
            }

            $count++;

            $max_id = $external_tw_entry_service->insertTweets($tweets, $stream->id);
            if (!$max_id) {
                $save_url = null;
                break;
            }
        }

        if ($save_url) {
            $this->external_tw_stream_service->updateUrl($stream->id, $save_url);
        }
    }

    /**
     * TwitterのユーザーIDでツイートを取得する
     * @param $twitter_user_id
     * @param $since_id Twitter ページングのsince_id
     * @param $max_id Twitter ページングのmax_id
     * @return bool|mixed
     */
    public function getTweetsByTwitterId($twitter_user_id, $since_id, $max_id) {
        // Sleep 15 minutes if reach rate limit
        if ($this->request_count >= self::TWITTER_API_RATE_LIMIT) {
            $this->logger->warn('GetExternalTwitterEntries: Temporary Stop Crawler in 15 minutes! ');
            sleep(self::TWITTER_RATE_LIMIT_WAITING);
            $this->request_count = 0;
        }

        try {
            $request_parameter = $this->createParameterForTwitterApi(self::MAX_TWEET, $since_id, $max_id, array('user_id' => $twitter_user_id));
            $response = $this->twitter->statuses_userTimeline($request_parameter);
            $this->request_count++;

            if ($this->getErrorMessage($response)) {
                throw new aafwException($this->getErrorMessage($response));
            }

            return $response;
        } catch (Exception $e) {
            $this->logger->error("GetExternalTwitterEntries#getTweetsByTwitterId failed! $twitter_user_id = {$twitter_user_id} and current request_count = {$this->request_count}");
            $this->logger->error($e);
            return false;
        }
    }
}