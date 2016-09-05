<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.vendor.twitter.Twitter');

class SyncTweetPosts extends BrandcoBatchBase {

    const API_CALL_LIMIT = 180;

    private $tweet_statuses = array(
        Twitter::TW_API_NOT_AUTHORIZED  => TweetMessage::TWEET_STATUS_PRIVATE,
        Twitter::TW_API_INVALID_ID      => TweetMessage::TWEET_STATUS_REMOVED,
        Twitter::TW_API_INVALID_PAGE    => TweetMessage::TWEET_STATUS_REMOVED   // Deactivate account
    );

    protected $data_builder;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->data_builder = aafwDataBuilder::newBuilder();
    }

    function executeProcess() {
        ini_set('memory_limit', '256M');

        $today = date('Y-m-d');

        $sync_tweet_batch_log_service = $this->service_factory->create('SyncTweetBatchLogService');
        $sync_tweet_batch_log = $sync_tweet_batch_log_service->getSyncTweetLog($today);

        if ($sync_tweet_batch_log) {
            $last_tweet_msg_id = $sync_tweet_batch_log->last_tweet_msg_id;
        } else {
            $last_tweet_msg_id = $this->getTweetMessagesMaxId();
            $sync_tweet_batch_log = $sync_tweet_batch_log_service->createEmptySyncTweetLog();
            $sync_tweet_batch_log->batch_date = $today;
            $sync_tweet_batch_log->last_tweet_msg_id = $last_tweet_msg_id;
        }

        $retweet_service = $this->service_factory->create('CpRetweetMessageService');
        $tweet_service = $this->service_factory->create('TweetMessageService');
        $logger = aafwLog4phpLogger::getDefaultLogger();

        $twitter = new Twitter(
            config('@twitter.Batch.ConsumerKey'),
            config('@twitter.Batch.ConsumerSecret'),
            config('@twitter.Batch.AccessToken'),
            config('@twitter.Batch.AccessTokenSecret')
        );

        try {
            $available_cp_tweet_action_ids = $this->getAvailableCpTweetAction($last_tweet_msg_id);
            $result = $this->getAvailableTweetMessages($available_cp_tweet_action_ids, $last_tweet_msg_id);

            if ($result !== null) {

                $index = 0;
                while ($tweet = $this->data_builder->fetch($result)) {
                    $tweet_id = $retweet_service->getTweetIdByTweetUrl($tweet->tweet_content_url);

                    if (!$tweet_id) continue;

                    $tweet_content = $twitter->getTweetContent($tweet_id);
                    $index += 1;

                    if (!$tweet_content) {
                        $api_result = $twitter->getApiResult();

                        if (!$api_result || !$api_result->errors) {
                            $logger->error('SyncTweetPosts TweetContent API Unknown Exception - Empty Result: ' . $api_result->errors);
                            continue;
                        }

                        $api_error = current($api_result->errors);

                        if ($api_error->code == Twitter::TW_API_RATE_LIMIT_EXCEEDED || $index > self::API_CALL_LIMIT) {
                            break;
                        }

                        if (!array_key_exists($api_error->code, $this->tweet_statuses)) {
                            $logger->error('SyncTweetPosts TweetContent Error ' . $api_error->code . ': ' . $api_error->message);
                            continue;
                        }

                        if ($tweet->tweet_status == $this->tweet_statuses[$api_error->code]) continue;

                        $tweet->tweet_status = $this->tweet_statuses[$api_error->code];
                        $tweet->approval_status = TweetMessage::APPROVAL_STATUS_REJECT;
                    } else {
                        if ($tweet->tweet_status != TweetMessage::TWEET_STATUS_PRIVATE) continue;

                        $tweet->tweet_status = TweetMessage::TWEET_STATUS_PUBLIC;
                    }

                    $tweet_service->saveTweetMessageData($tweet);
                }

                if ($tweet) {
                    $sync_tweet_batch_log->last_tweet_msg_id = $tweet->id;
                }
            }

            $sync_tweet_batch_log_service->updateSyncTweetLog($sync_tweet_batch_log);
        } catch (Exception $e) {
            throw new aafwException('SyncTweetPosts Exception ' . var_export($e));
        }
    }

    /**
     * @param $last_tweet_msg_id
     * @return array
     */
    public function getAvailableCpTweetMessageData($last_tweet_msg_id) {
        $query = "SELECT tm.cp_user_id, tm.cp_tweet_action_id FROM tweet_messages tm WHERE tm.del_flg = 0 AND tm.id <= " . $last_tweet_msg_id . " AND tm.tweet_status = " . TweetMessage::TWEET_STATUS_PUBLIC . " AND tm.tweet_content_url <> '' GROUP BY tm.cp_tweet_action_id ORDER BY tm.id DESC";

        return $this->data_builder->getBySQL($query, array(array('__NOFETCH__' => true)));
    }

    /**
     * @param $last_tweet_msg_id
     * @return array
     */
    public function getAvailableCpTweetAction($last_tweet_msg_id) {
        $available_cp_tweet_action_ids = array();
        $cp_flow_service = $this->service_factory->create('CpFlowService');
        $cp_user_service = $this->service_factory->create('CpUserService');

        $tweet_msg_data = $this->getAvailableCpTweetMessageData($last_tweet_msg_id);

        while ($tweet_msg = $this->data_builder->fetch($tweet_msg_data)) {
            $cp_user = $cp_user_service->getCpUserById($tweet_msg['cp_user_id']);
            $cp = $cp_user->getCp();

            if (!$cp_flow_service->isPublicCp($cp)) continue;

            $available_cp_tweet_action_ids[] = $tweet_msg['cp_tweet_action_id'];
        }

        return $available_cp_tweet_action_ids;
    }

    /**
     * @param $tweet_action_ids
     * @param $last_tweet_msg_id
     * @return array|null
     */
    public function getAvailableTweetMessages($tweet_action_ids, $last_tweet_msg_id) {
        if (!$tweet_action_ids || !is_array($tweet_action_ids)) {
            return null;
        }

        $query = "SELECT * FROM tweet_messages tm WHERE tm.del_flg = 0 AND tm.id <= " . $last_tweet_msg_id . " AND tm.tweet_status = " . TweetMessage::TWEET_STATUS_PUBLIC . " AND tm.tweet_content_url <> '' AND tm.cp_tweet_action_id IN (" . join(',', $tweet_action_ids) . ") ORDER BY tm.id DESC";

        return $this->data_builder->getBySQL($query, array(array('__NOFETCH__' => true), null, array(), false, 'TweetMessage'));
    }

    /**
     * @return array
     */
    public function getTweetMessagesMaxId() {
        $query = "SELECT MAX(id) max_id FROM tweet_messages tm";

        $result = $this->data_builder->getBySQL($query, array(array('__NOFETCH' => true)));
        return $result[0]['max_id'];
    }
}