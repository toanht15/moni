<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.batch.GetTwitterDataBatchBase');

class GetTwitterReplyTweets extends GetTwitterDataBatchBase {

    const PARTITIONING_FACTOR = 7;
    const MAX_REPLYTWEET = 200;
    const TWITTER_API_LIMIT = 15;

    protected $streams_tables = array(
        self::TYPE_INTERNAL => 'twitter_streams',
        self::TYPE_EXTERNAL => 'external_tw_streams'
    );
    protected $twitter_uid;

    public function executeProcess() {
        /** @var  TwEntriesUsersMentionService $tw_entries_users_mention_service */
        $tw_entries_users_mention_service = $this->service_factory->create('TwEntriesUsersMentionService');
        /** @var DetailCrawlerUrlService $detail_crawler_url_service */
        $detail_crawler_url_service = $this->service_factory->create('DetailCrawlerUrlService');
        /** @var TwEntriesUsersReplyService $tw_entries_users_reply_service */
        $tw_entries_users_reply_service = $this->service_factory->create('TwEntriesUsersReplyService');

        $listTwitterStreams = $this->getAllTwitterStreams();
        $this->twitter = $this->initTwitterClient();

        foreach ($listTwitterStreams as $twitterStream) {
            try {
                //Update trawler twitter log
                $this->updateCrawlerTwitterLog($twitterStream['id'], $twitterStream['type'], CrawlerTwitterLog::CRAWLER_TYPE_REPLY);

                //Init Twitter with Token And Token Secret of Brand Social Account
                $this->twitter->setToken($twitterStream['token'], $twitterStream['token_secret']);
                $verify_twitter = $this->twitter->account_verifyCredentials();

                if ($this->getErrorMessage($verify_twitter)) {
                    throw new aafwException("Twitter authenticate failed! stream_id = {$twitterStream['id']} and stream_type = {$twitterStream['type']} " . $this->getErrorMessage($verify_twitter));
                }

                $this->twitter_uid = $verify_twitter['id'];
                $max_object_id = $tw_entries_users_mention_service->getMaxObjectIdByMentionedUid($this->twitter_uid);
                $min_object_id = $tw_entries_users_mention_service->getMinObjectIdByMentionedUid($this->twitter_uid);

                $mentions = $this->getMentionTimeLine($max_object_id, $min_object_id);

                if (!$tw_entries_users_mention_service->insertMentions($mentions)) {
                    continue;
                }

                $replies = array();
                foreach ($mentions as $mention) {
                    //If mention are reply tweet
                    if ($mention['entry_object_id']) {
                        $mention['mention_id'] = $tw_entries_users_mention_service->getMentionByObjectId($mention['object_id'])->id;
                        $replies[] = $mention;

                        //Update detail crawler url
                        $detail_crawler_url_service->updateDetailCrawlerUrl($mention['entry_object_id'], $twitterStream['type'], DetailCrawlerUrl::CRAWLER_TYPE_TWITTER, DetailCrawlerUrl::DATA_TYPE_REPLY, "");
                    }
                }
                $tw_entries_users_reply_service->insertReplies($replies);

                //Exit Batch If reached rate limit
                if($this->rate_limit_exceeded){
                    $this->logger->warn('GetTwitterReplyTweets: Twitter API Rate Limit. Exit Batch!');
                    exit();
                }

            } catch (Exception $e) {
                $this->logger->error("GetTwitterReplyTweets#executeProcess error! current_twitter_stream: stream_id = {$twitterStream['id']} and stream_type = {$twitterStream['type']}");
                $this->logger->error($e);
                continue;
            }
        }
    }

    public function getAllTwitterStreams() {
        $result = array();

        foreach ($this->streams_tables as $entry_type => $key) {
            list($min_id, $max_id) = $this->getMinMaxID($key);

            if ($max_id === 0) {
                $this->logger->warn("There are no data in the {$key} table!");
                continue;
            }
            list($start_id, $end_id) = $this->getDataRangeByWeek($max_id, $min_id);

            $streams = $this->getTwitterStreamsByType($entry_type, $start_id, $end_id);

            foreach ($streams as $stream) {
                $result[] = $stream;
            }
        }
        return $result;
    }

    /**
     * @param $max_id
     * @param $min_id
     * @return array
     */
    public function getDataRangeByWeek($max_id, $min_id) {
        $partitioning_no = date("N");
        $partitioning_factor = self::PARTITIONING_FACTOR;

        $start_id = (int)(($max_id - $min_id) * ($partitioning_no - 1) / $partitioning_factor) + $min_id;
        $end_id = (int)(($max_id - $min_id) * $partitioning_no / $partitioning_factor) + $min_id;

        return array($start_id, $end_id);
    }

    /**
     * @param $entry_type
     * @param $start_id
     * @param $end_id
     * @return array
     */
    public function getTwitterStreamsByType($entry_type, $start_id, $end_id) {
        $result = array();
        $last_crawler_stream = $this->getLastCrawlerObjectByDate($entry_type, CrawlerTwitterLog::CRAWLER_TYPE_REPLY);

        if ($last_crawler_stream) {
            $start_id = $last_crawler_stream->last_id;
        }

        //SQL conditions
        $conditions = array(
            null,
            array(
                'name' => 'id',
                'direction' => 'asc'
            )
        );

        if ($entry_type == self::TYPE_INTERNAL) {
            $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

            $query = "SELECT id,brand_social_account_id FROM {$this->streams_tables[$entry_type]} WHERE del_flg = 0 AND id BETWEEN " . $start_id . " AND " . $end_id;
            $streams = $this->db->getBySQL($query, $conditions);

            foreach ($streams as $stream) {
                $brandSocialAccount = $brand_social_account_service->getBrandSocialAccountById($stream['brand_social_account_id']);
                $stream['token'] = $brandSocialAccount->token;
                $stream['token_secret'] = $brandSocialAccount->token_secret;
                $stream['social_media_account_id'] = $brandSocialAccount->social_media_account_id;
                $stream['type'] = $entry_type;
                $result[] = $stream;
            }

        } else {
            $query = "SELECT id,token,token_secret,social_media_account_id FROM {$this->streams_tables[$entry_type]} WHERE del_flg = 0 AND id BETWEEN " . $start_id . " AND " . $end_id;
            $streams = $this->db->getBySQL($query, $conditions);

            foreach ($streams as $stream) {
                if (!$stream['token'] || !$stream['token_secret']) {
                    continue;
                }
                $stream['type'] = $entry_type;
                $result[] = $stream;
            }
        }

        return $result;
    }

    /**
     * User AuthでTwitter Clientを作成する
     * @return \Codebird\Codebird
     */
    protected function initTwitterClient() {
        \Codebird\Codebird::setConsumerKey(
            config('@twitter.Admin.ConsumerKey'),
            config('@twitter.Admin.ConsumerSecret')
        );
        $client = \Codebird\Codebird::getInstance();
        $client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

        return $client;
    }

    /**
     * @param $max_object_id
     * @param $min_object_id
     * @return array
     */
    private function getMentionTimeLine($max_object_id, $min_object_id) {
        $since_id = $max_object_id ?: null; //twitter api paging value
        $max_id = $min_object_id ?: null; // twitter api paging value

        //get older mentions
        $older_mentions = array();
        if ($max_id) {
            $older_mentions = $this->getMentionFromApiResponses(null, $max_id);
        }

        //get newer mentions
        $newer_mentions = $this->getMentionFromApiResponses($since_id, null);

        $mentions = array_merge($newer_mentions, $older_mentions);

        return $mentions;
    }

    /**
     * @param $since_id
     * @param $max_id
     * @return array
     */
    private function getMentionFromApiResponses($since_id, $max_id){
        $mentions = array();

        while(1){
            $responses = $this->getMentionsByTwitterApi($since_id, $max_id);

            if(Util::isNullOrEmpty($responses)){
                break;
            }

            $responses = $this->removeDuplicateElementById($responses, $max_id);

            if(is_array($responses) && count($responses) <= 1){
                break;
            }

            foreach ($responses as $value) {
                if (!is_array($value)) {
                    continue;
                }
                $max_id = $value['id'];

                $mention = array();
                $mention['mentioned_uid'] = $this->twitter_uid;
                $mention['tw_uid'] = $value['user']['id'];
                $mention['text'] = $value['text'];
                $mention['object_id'] = $value['id_str'];
                $mention['entry_object_id'] = $value['in_reply_to_status_id_str'];
                $mentions[] = $mention;
            }

        }

        return $mentions;
    }

    /**
     * @param $since_id
     * @param $max_id
     * @return bool|mixed
     */
    public function getMentionsByTwitterApi($since_id, $max_id) {
        try {
            if ($this->request_count >= self::TWITTER_API_LIMIT) {
                $this->rate_limit_exceeded = true;
                return null;
            }
            $request_parameter = $this->createParameterForTwitterApi(self::MAX_REPLYTWEET, $since_id, $max_id, array());

            $response = $this->twitter->statuses_mentionsTimeline($request_parameter);
            $this->request_count++;

            if($this->getErrorCode($response) == self::RATE_LIMIT_ERROR_CODE){
                $this->rate_limit_exceeded = true;
                return null;
            }

            if ($this->getErrorMessage($response)) {
                throw new aafwException($this->getErrorMessage($response));
            }

            return $response;
        } catch (Exception $e) {
            $this->logger->error('GetTwitterReplyTweets#getMentionsByTwitterApi Get Twitter Mention Failed!');
            $this->logger->error($e);
            return null;
        }
    }

}