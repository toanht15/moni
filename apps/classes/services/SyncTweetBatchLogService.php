<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SyncTweetBatchLogService extends aafwServiceBase {
    private $sync_tweet_batch_logs;

    public function __construct() {
        $this->sync_tweet_batch_logs = $this->getModel('SyncTweetBatchLogs');
    }

    public function createEmptySyncTweetLog() {
        return $this->sync_tweet_batch_logs->createEmptyObject();
    }

    public function updateSyncTweetLog($sync_tweet_batch_log) {
        $this->sync_tweet_batch_logs->save($sync_tweet_batch_log);
    }

    public function getSyncTweetLog($date) {
        $filter = array(
            'batch_date' => $date
        );
        return $this->sync_tweet_batch_logs->findOne($filter);
    }
}