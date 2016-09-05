<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CrawlerTwitterLogService extends aafwServiceBase {

    private $crawler_twitter_logs;

    public function __construct() {
        $this->crawler_twitter_logs = $this->getModel('CrawlerTwitterLogs');
    }

    public function createEmptyCrawlerTwitterLog() {
        return $this->crawler_twitter_logs->createEmptyObject();
    }

    public function updateCrawlerTwitterLog($crawler_twitter_log) {
        $this->crawler_twitter_logs->save($crawler_twitter_log);
    }

    public function getCrawlerTwitterLog($batch_date, $type, $crawler_type) {
        $filter = array(
            'batch_date' => $batch_date,
            'crawler_type' => $crawler_type,
            'type' => $type
        );

        return $this->crawler_twitter_logs->findOne($filter);
    }
}