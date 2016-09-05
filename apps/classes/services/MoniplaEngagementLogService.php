<?php
//SNSのボタンのログに関するテーブルを操作するクラス

class MoniplaEngagementLogService extends aafwServiceBase {
    
    private $moniplaEngagementLogStore;

    public function __construct() {
        $this->moniplaEngagementLogStore = $this->getModel('MoniplaEngagementLogs');
    }

    /**
     * @param $log
     * @return mixed
     */
    public function createLog($log) {
        $monipla_engagement = $this->moniplaEngagementLogStore->createEmptyObject();

        $monipla_engagement->social_media_id = $log['social_media_id'];
        $monipla_engagement->locate_id          = $log['locate_id'];
        $monipla_engagement->value              = $log['value'];
        $monipla_engagement->user_id            = $log['user_id'];
        $monipla_engagement->clicked_at         = date('Y/m/d H:i:s');

        return $this->moniplaEngagementLogStore->save($monipla_engagement);
    }
}