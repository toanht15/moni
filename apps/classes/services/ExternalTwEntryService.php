<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ExternalTwEntryService extends aafwServiceBase {

    const TARGET_TYPE_NORMAL = 0;
    const TARGET_TYPE_BLANK = 1;
    protected $external_tw_entries;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->external_tw_entries = $this->getModel('ExternalTwEntries');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $tweets
     * @param $stream_id
     * @return bool|null
     */
    public function insertTweets($tweets, $stream_id) {
        $max_id = null;
        $count = 0;
        $sql = "INSERT INTO external_tw_entries(stream_id, object_id, target_type, link, extra_data, created_at, updated_at) VALUES";

        $this->external_tw_entries->begin();
        try {
            foreach ($tweets as $tweet) {
                if (!is_array($tweet)) {
                    continue;
                }
                $addTweet = $this->createTweetData($tweet,$stream_id);
                $max_id = $tweet['id'];

                $sql .= "({$addTweet['stream_id']},{$addTweet['object_id']}, {$addTweet['target_type']},'{$addTweet['link']}','{$addTweet['extra_data']}', NOW(), NOW()),";
                $count++;
            }

            if ($count > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= "ON DUPLICATE KEY UPDATE updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

            $this->external_tw_entries->commit();

            return $max_id;
        } catch (Exception $e) {
            $this->external_tw_entries->rollback();
            $this->logger->error("ExternalTwEntryService#insertTweets Failed stream_id = {$stream_id}");
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * @param $tweet
     * @param $stream_id
     * @return array
     */
    private function createTweetData($tweet, $stream_id) {
        $addTweet = array();
        $addTweet['stream_id'] = $stream_id;
        $addTweet['object_id'] = $tweet['id_str'];
        $addTweet['target_type'] = self::TARGET_TYPE_BLANK;
        $addTweet['link'] = "https://twitter.com/{$tweet['user']['screen_name']}/status/{$tweet['id_str']}";
        $addTweet['extra_data'] = json_encode($tweet);
        $addTweet['extra_data'] = $this->external_tw_entries->escapeForSQL($addTweet['extra_data']);

        return $addTweet;
    }
}