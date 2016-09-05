<?php
AAFW::import('jp.aainc.classes.batch.GetTwitterDataBatchBase');
require_once('vendor/codebird-php/src/codebird.php');

class GetTwitterFollowingUsers extends GetTwitterDataBatchBase {

    const TWITTER_CRAWLER_APPS_COUNT = 4;
    const TWITTER_FOLLOWER_MAX_COUNT = 5000;
    const FOLLOWER_UPDATE_COUNT = 100; //count to update follower
    const TWITTER_RATE_LIMIT = 15;

    protected $twitter_follow_service;
    protected $executed_stream_count;

    public function __construct() {
        parent::__construct();
        $this->twitter_crawler_apps = Config("@twitter.TwitterAppsForCrawler");
        $this->twitter_follow_service = $this->service_factory->create("TwitterFollowService");
        $this->executed_stream_count = 0;
    }

    public function executeProcess() {
        /** @var DetailCrawlerUrlService $detail_crawler_url_service */
        $detail_crawler_url_service = $this->service_factory->create("DetailCrawlerUrlService");

        try {
            $twitter_stream_accounts = $this->getTwitterStreamAccounts();

            if (!$this->build()) {
                throw new aafwException("{$this->execute_class}: Twitter Authenticate failed! current_app_number: " . $this->used_app_count);
            }

            foreach ($twitter_stream_accounts as $account) {
                $this->executed_stream_count++;
                $crawler_url = $detail_crawler_url_service->getDetailCrawlerUrlByObjectId($account['twitter_id'], DetailCrawlerUrl::CRAWLER_TYPE_TWITTER, DetailCrawlerUrl::DATA_TYPE_FOLLOW);

                list($next_cursor, $count) = $this->getParameterForTwitterApi($crawler_url);

                while (1) {
                    $twitter_followers = $this->getUserFollower($account['twitter_id'], $next_cursor, $count);

                    //Exit Batch If Rate limit
                    if (!$twitter_followers && $this->rate_limit_exceeded == true) {
                        $this->logger->warn('Rate Limit! Exit Batch GetTwitterFollowingUsers!');
                        exit();
                    }

                    $next_cursor = $this->getNextCursor($twitter_followers, $account['stream_id']);
                    $this->twitter_follow_service->insertTwitterFollows($twitter_followers['ids'], $account['stream_id']);

                    // Update detail url
                    $detail_crawler_url_service->updateDetailCrawlerUrl($account['twitter_id'], self::TYPE_INTERNAL, DetailCrawlerUrl::CRAWLER_TYPE_TWITTER, DetailCrawlerUrl::DATA_TYPE_FOLLOW, $next_cursor);
                    // Update Crawler Twitter log
                    $this->updateCrawlerTwitterLog($account['stream_id'], self::TYPE_INTERNAL, CrawlerTwitterLog::CRAWLER_TYPE_FOLLOW);

                    //次のペジがない場合は
                    if (Util::isNullOrEmpty($next_cursor)) {
                        break;
                    }
                }
            }

            $this->logger->warn('GetTwitterFollowingUsers#executeProcess: Get Twitter following users success! executed_stream_count = ' . $this->executed_stream_count);
        } catch (Exception $e) {
            $this->logger->error('GetTwitterFollowingUsers#executeProcess: Get Twitter following users error! executed_stream_count = ' . $this->executed_stream_count);
            $this->logger->error($e);
        }
    }

    /**
     * @return mixed
     */
    private function getTwitterStreamAccounts() {
        $last_crawler_twitter_stream = $this->getLastCrawlerObjectByDate(self::TYPE_INTERNAL, CrawlerTwitterLog::CRAWLER_TYPE_FOLLOW);
        $last_crawler_twitter_stream_id = $last_crawler_twitter_stream->last_id;

        $params = array(
            'last_crawler_stream_id' => $last_crawler_twitter_stream_id ?: 0
        );
        $twitter_accounts = $this->db->getTwitterStreamSocialAccounts($params);

        return $twitter_accounts;
    }

    /**
     * @param $crawler_url
     * @return array
     */
    private function getParameterForTwitterApi($crawler_url) {
        if (!$crawler_url) {
            $next_cursor = null;
        } else {
            $next_cursor = $crawler_url->url;
        }

        if ($crawler_url && Util::isNullOrEmpty($next_cursor)) {
            $count = self::FOLLOWER_UPDATE_COUNT;
        } else {
            $count = self::TWITTER_FOLLOWER_MAX_COUNT;
        }

        return array($next_cursor, $count);
    }

    /**
     * @param $twitter_uid
     * @param $next_cursor
     * @param $count
     * @return mixed
     */
    private function getUserFollower($twitter_uid, $next_cursor, $count) {
        try {
            if ($this->request_count >= self::TWITTER_RATE_LIMIT) {

                //If used all apps
                if ($this->used_app_count >= self::TWITTER_CRAWLER_APPS_COUNT) {
                    $this->rate_limit_exceeded = true;
                    return null;
                }

                $this->fetchNewTwitterApp();
                $this->build();
                $this->logger->warn('Exchange to twitter crawler app : ' . $this->used_app_count);
            }

            $cursor = $next_cursor ?: -1;
            $params = array(
                'user_id' => $twitter_uid,
                'count' => $count,
                'cursor' => $cursor
            );
            $response = $this->twitter->followers_ids($params);
            $this->request_count++;

            if ($this->getErrorCode($response) == self::RATE_LIMIT_ERROR_CODE) {
                $this->rate_limit_exceeded = true;
                return null;
            }

            if ($this->getErrorMessage($response)) {
                throw new aafwException($this->getErrorMessage($response));
            }

            return $response;
        } catch (Exception $e) {
            $this->logger->error("{$this->execute_class}#GetUserFollowing: Get following user Failed!");
            $this->logger->error($e);
            return null;
        }
    }

    /**
     * 次のページcursorを取得する
     * @param $twitter_followers
     * @param $stream_id
     * @return null
     */
    private function getNextCursor($twitter_followers, $stream_id) {
        $last_follower_id = array_pop($twitter_followers['ids']);
        $next_cursor = null;

        //最後のFollower_idsを取得しない場合は次のページを取得する
        if (!$this->twitter_follow_service->getTwitterFollowByStreamIdAndFollowerId($stream_id, $last_follower_id)) {
            $next_cursor = ($twitter_followers['next_cursor'] != 0) ? $twitter_followers['next_cursor'] : NULL;
        }

        return $next_cursor;
    }
}