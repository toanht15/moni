<?php
require_once dirname(__FILE__) . '/../../config/define.php';

AAFW::import('jp.aainc.classes.CacheManager');

/**
 * 手動実行用の古いpsessをクリアするためのバッチです。
 * 8/1のドメイン移行以前の古いデータをクリアするために作成しました。
 *
 * Class OldPsessCleaner
 */
class OldPsessCleaner {

    function execute() {
        $max_ttl = 15552000; // 180 days
        $twelve_day_in_time = 3600 * 24 * 12;
        $redis = CacheManager::getRedis();
        $psess_keys = $redis->keys("psess:*");

        $targets = array();
        foreach ($psess_keys as $psess_key) {
            $ttl = $redis->ttl($psess_key);
            $elasped_time = $max_ttl - $ttl;
            if ($elasped_time > $twelve_day_in_time) {
                $targets[] = $psess_key;
            }

            if (count($targets) > 100) {
                $redis->del($targets);
                $targets = array();
            }
        }

        if (count($targets) > 0) {
            $redis->del($targets);
        }
    }
}