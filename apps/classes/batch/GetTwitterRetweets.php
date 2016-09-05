<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.batch.GetTwitterDataBatchBase');

class GetTwitterRetweets extends GetTwitterDataBatchBase {

    const MAX_RETWEET = 100;
    const TWITTER_API_RATE_LIMIT = 60;
    const ENTRY_LIMIT = 200;

    protected $entries_tables = array(
        self::TYPE_INTERNAL => 'twitter_entries',
        self::TYPE_EXTERNAL => 'external_tw_entries'
    );

    protected $executed_entries_count;
    private $current_entry_object_id;

    public function __construct() {
        parent::__construct();
        $this->twitter_crawler_apps = config('@twitter.TwitterAppsForCrawler');
        $this->executed_entries_count = 0;
    }

    public function executeProcess() {
        /** @var DetailCrawlerUrlService $detail_crawler_url_service */
        $detail_crawler_url_service = $this->service_factory->create('DetailCrawlerUrlService');
        /** @var TwEntriesUsersRetweetService $tw_entries_users_retweet_service */
        $tw_entries_users_retweet_service = $this->service_factory->create('TwEntriesUsersRetweetService');

        try {
            $entries = $this->getAllEntries();

            //Init Twitter Client
            if (!$this->build()) {
                throw new aafwException('Twitter Authenticate failed! Current App Info: ConsumerKey: ' . $this->current_twitter_app['ConsumerKey'] .
                    ' - ConsumerSecret: ' . $this->current_twitter_app['ConsumerSecret']);
            }

            foreach ($entries as $entry) {
                $this->executed_entries_count++;
                $this->current_entry_object_id = $entry['object_id'];

                $retweets = $this->getRetweetsByTweetId($entry['object_id']);

                //Check Rate limit
                if($this->rate_limit_exceeded){
                    //Exit Batch
                    $this->logger->warn('GetTwitterRetweets: Twitter API Rate Limit. Exit Batch!');
                    exit();
                }

                if(!$retweets){
                    continue;
                }

                //Update to detail crawler url
                $detail_crawler_url_service->updateDetailCrawlerUrl($entry['object_id'], $entry['type'], DetailCrawlerUrl::CRAWLER_TYPE_TWITTER, DetailCrawlerUrl::DATA_TYPE_RETWEET, "");

                //Update to crawler twitter log
                $this->updateCrawlerTwitterLog($entry['id'], $entry['type'],CrawlerTwitterLog::CRAWLER_TYPE_RETWEET);

                if (count($retweets) == 1 || !$retweets) continue;

                $tw_entries_users_retweet_service->insertRetweets($retweets, $entry['object_id']);
            }

            $this->logger->warn('GetTwitterRetweets#executeProcess: Get retweets success! executed_entries_count = ' . $this->executed_entries_count . ' - current_entry_object_id = ' . $this->current_entry_object_id);
        } catch (Exception $e) {
            $this->logger->error('GetTwitterRetweets#executeProcess: Get retweets error! executed_entries_count = ' . $this->executed_entries_count . ' - current_entry_object_id = ' . $this->current_entry_object_id);
            $this->logger->error($e);
        }

    }

    /**
     * @return array
     */
    public function getAllEntries() {
        $result = array();

        foreach ($this->entries_tables as $entry_type => $value) {
            list($min_id, $max_id) = $this->getMinMaxID($value);

            if ($max_id === 0) {
                $this->logger->warn("There are no data in the '{$value}' table!");
                continue;
            }
            $entries = $this->getAllEntriesByType($entry_type, $max_id, $min_id);

            foreach ($entries as $entry) {
                $entry['type'] = $entry_type;
                $result[] = $entry;
            }
        }

        return $result;
    }

    /**
     * @param $entry_type
     * @param $max_id
     * @param $min_id
     * @return array
     */
    public function getAllEntriesByType($entry_type, $max_id, $min_id) {
        list($start_id, $end_id) = $this->getDataRangeByMonth($max_id, $min_id);
        $last_crawler_entry = $this->getLastCrawlerObjectByDate($entry_type, CrawlerTwitterLog::CRAWLER_TYPE_RETWEET);

        if ($last_crawler_entry) {
            if ($last_crawler_entry->last_id >= $end_id) {
                return null;
            }
            $start_id = $last_crawler_entry->last_id + 1;
        }

        $query = "SELECT id,object_id FROM " . $this->entries_tables[$entry_type] . " WHERE del_flg = 0 AND id BETWEEN " . $start_id . " AND " . $end_id;
        $order = array(
            'name' => 'id',
            'direction' => 'asc'
        );
        $condition = array(null, $order, array('count' => self::ENTRY_LIMIT));
        $entries = $this->db->getBySQL($query, $condition);

        return $entries;
    }

    /**
     * @param $max_id
     * @param $min_id
     * @return array
     */
    public function getDataRangeByMonth($max_id, $min_id) {
        $partitioning_no = date("j");
        $partitioning_factor = date("t");

        $start_id = (int)(($max_id - $min_id) * ($partitioning_no - 1) / $partitioning_factor) + $min_id;
        $end_id = (int)(($max_id - $min_id) * $partitioning_no / $partitioning_factor) + $min_id;

        return array($start_id, $end_id);
    }

    /**
     * TwitterAPIでリツイートを取得する
     *
     * @param $tweetId
     * @return mixed
     */
    public function getRetweetsByTweetId($tweetId) {
        try {
            if ($this->request_count >= self::TWITTER_API_RATE_LIMIT) {
                $this->fetchNewTwitterApp();
                $this->build();
                $this->logger->warn('Exchanged Twitter App to crawler! ');
            }

            $params = array(
                'count' => self::MAX_RETWEET,
                'id' => $tweetId
            );
            $response = $this->twitter->statuses_retweets_ID($params);
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
            $this->logger->error("GetTwitterRetweets#getRetweetsByTweetId failed! tweetId = {$tweetId}");
            $this->logger->error($e);
            return null;
        }
    }
}