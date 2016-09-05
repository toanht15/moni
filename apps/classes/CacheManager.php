<?php

AAFW::import('jp.aainc.lib.db.aafwRedisManager');

class CacheManager {

    const TTL = 900;

    const KEY_BRAND_INFO_CONTAINER = ':bic:';

    const KEY_BRAND_PANEL = ':bp:';

    const KEY_SNS_PANEL = ':sp:';

    const KEY_NOTIFICATION_COUNT = ':nc:';

    const KEY_BRAND_FAN_COUNT = ':bfc:';

    const KEY_CAMPAIGN_LP_INFO = ":cli:";

    const KEY_SCHEMA = ":dts:";

    const KEY_CPI = ":cpi:";

    const KEY_CAI =":cai:";

    const KEY_POPULAR_CP_INFO = ":pci";

    const JSON_KEY_UNREAD_MESSAGE_COUNT = 'umc';

    const JSON_KEY_UPDATED_AT = 'ua';

    //preview key
    const PREVIEW_PREFIX = "preview";
    //notification preview
    const BRAND_NOTIFICATION_PREVIEW = "brand_notification_preview";
    // static html preview
    const PAGE_PREVIEW_KEY = "page_preview";
    //categories preview
    const CATEGORIES_PREVIEW_KEY = "categories_preview";
    // comment plugin preview
    const COMMENT_PLUGIN_PREVIEW_KEY = 'comment_plugin_preview';
    //signup preview
    const SIGNUP_PREVIEW_KEY = "sign_up_preview";
    // free area preview
    const FREE_AREA_PREVIEW_KEY = "free_area_preview";
    //authentication page preview
    const AUTHENTICATION_PAGE_PREVIEW_KEY = "authentication_page_preview";

    /** @var CacheManager_Connection connection  */
    private static $connection = null;
    private $prefix;
    private $logger;
    private $debug;
    private $schema_ttl;

    public function __construct($prefix = 'cache') {
        $this->prefix = $prefix;
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->debug = DEBUG;
        $this->schema_ttl = config("Store.SchemaCacheInterval");
    }

    public function addPanelCache($brand_id, $cache_type, $media_type, $page, $panel_list) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_PANEL . $brand_id;
        $field =  $cache_type . ":" . $media_type . ":" . $page;
        $redis->watch($key);
        $redis->multi();
        $redis->hSet($key, $field, json_encode($panel_list));
        $redis->exec();
    }

    public function getPanelCache($brand_id, $cache_type, $media_type, $page) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_PANEL . $brand_id;
        $field = $cache_type . ":" . $media_type . ":" . $page;
        $value = $redis->hGet($key, $field);
        if ($value === false) {
            return null;
        }

        return json_decode($value, true);
    }

    public function addSnsPanelCache($brand_id, $panel_list, $cache_type = "all", $media_type = "pc") {
        $redis = self::getRedis();
        $key = $this->prefix. self::KEY_SNS_PANEL . $brand_id;
        $field = $cache_type. ':' .$media_type;
        $redis->watch($key);
        $redis->multi();
        $redis->hSet($key, $field, json_encode($panel_list));
        $redis->exec();
    }

    public function getSnsPanelCache($brand_id, $cache_type = "all", $media_type = "pc") {
        $redis = self::getRedis();
        $key = $this->prefix. self::KEY_SNS_PANEL . $brand_id;
        $field = $cache_type. ":".$media_type;
        $value = $redis->hGet($key, $field);
        if($value == false){
            return null;
        }

        return json_decode($value, true);
    }

    public function deleteSnsPanelCache($brand_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_SNS_PANEL . $brand_id;
        $redis->del($key);
    }

    public function deletePanelCache($brand_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_PANEL . $brand_id;
        $redis->del($key);
    }

    public function addCache($key, $val, $params = null){
        if(!$params) $params = array();
        try {
            $redis = self::getRedis();
            $key = $this->generateKey($key, $params);
            $redis->set($key, $val);
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    public function addCacheWithTimeout($key, $val, $params = null, $ttl = self::TTL) {
        if(!$params) $params = array();
        try {
            $redis = self::getRedis();
            $key = $this->generateKey($key, $params);
            $redis->set($key, $val, $ttl);
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    public function getCache($key, $params = null) {
        if(!$params) $params = array();
        try {
            $redis = self::getRedis();
            $key = $this->generateKey($key, $params);
            if ($redis->exists($key)) {
                return json_decode($redis->get($key), true);
            } else {
                return null;
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    public function deleteCache($key, $params = null) {
        if(!$params) $params = array();
        try {
            $redis = self::getRedis();
            $key = $this->generateKey($key, $params);
            if ($redis->exists($key)) {
                $redis->del($key);
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    public function beginBatch() {
        self::getRedis()->multi(Redis::PIPELINE);
    }

    public function flushBatch() {
        self::getRedis()->exec();
    }

    public function resetBatch() {
        self::getRedis()->discard();
    }

    private function generateKey($key, $params = null){
        $key = $this->prefix . ":" . $key;
        if ($params) {
            foreach ($params as $item) {
                $key .= ":" . $item;
            }
        }
        return $key;
    }

    public function getNotificationCount($brand_id, $user_id) {
        $key = $this->prefix . self::KEY_NOTIFICATION_COUNT . $brand_id;
        $value = self::getRedis()->hGet($key, $user_id);
        if ($value === false) {
            return null;
        }
        return json_decode($value, true);
    }

    public function setNotificationCount($value, $brand_id, $user_id) {
        $key = $this->prefix . self::KEY_NOTIFICATION_COUNT . $brand_id;
        self::getRedis()->hSet($key, $user_id, json_encode($value));
    }

    public function resetNotificationCount($brand_id, $user_id) {
        $key = $this->prefix . self::KEY_NOTIFICATION_COUNT . $brand_id;
        self::getRedis()->hDel($key, $user_id);
    }

    public function setMessageHistoryCache($cp_action_id, $value) {
        $this->addCache('message_delivered_history', $value, array($cp_action_id));
    }

    public function getMessageHistoryCache($cp_action_id) {
        return $this->getCache('message_delivered_history', array($cp_action_id));
    }

    public function deleteMessageHistoryCache($cp_action_id) {
        $this->deleteCache('message_delivered_history', array($cp_action_id));
    }

    public function getBrandInfo($brand_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_INFO_CONTAINER . $brand_id;
        $value = $redis->get($key);
        if ($value) {
            return unserialize($value);
        } else {
            return null;
        }
    }

    public function setBrandInfo($brand_id, $entities) {
        $serialized = serialize($entities);
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_INFO_CONTAINER . $brand_id;
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $serialized);
        $redis->expire($key, self::TTL);
        $redis->exec();
    }

    public function clearBrandInfo($brand_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_INFO_CONTAINER . $brand_id;
        $redis->del($key);
    }

    public function getBrandFanCount($brand_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_FAN_COUNT . $brand_id;
        return $redis->get($key);
    }

    public function setBrandFanCount($brand_id, $fan_count) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_BRAND_FAN_COUNT . $brand_id;
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $fan_count);
        $redis->expire($key, self::TTL);
        $redis->exec();
    }

    public function getCampaignLPInfo($cp_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAMPAIGN_LP_INFO . $cp_id;
        $value = $redis->get($key);
        if ($value) {
            if (!$this->debug) {
                $value = gzuncompress($value);
            }
            $value = unserialize($value);
        }
        return $value;
    }

    public function setCampaignLPInfo($cp_id, $info) {
        $serialized = serialize($info);
        if (!$this->debug) {
            $serialized = gzcompress($serialized);
        }
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAMPAIGN_LP_INFO . $cp_id;
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $serialized);
        $redis->expire($key, self::TTL);
        $redis->exec();
    }

    public function clearCampaignLPInfo($cp_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAMPAIGN_LP_INFO . $cp_id;
        $redis->del($key);
    }

    public function getCpInfo($cp_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CPI . $cp_id;
        $value = $redis->get($key);
        $unserialized = unserialize($value);
        return $unserialized;
    }

    public function setCpInfo($cp_id, $cp) {
        $serialized = serialize($cp);
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CPI . $cp_id;
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $serialized);
        $redis->expire($key, self::TTL);
        $redis->exec();
    }

    public function clearCpInfo($cp_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CPI . $cp_id;
        return $redis->del($key);
    }

    public function getCpActionInfo($cp_action_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAI . $cp_action_id;
        $value = $redis->get($key);
        $unserialized = unserialize($value);
        return $unserialized;
    }

    public function setCpActionInfo($cp_action_id, $cp_action) {
        $serialized = serialize($cp_action);
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAI . $cp_action_id;
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $serialized);
        $redis->expire($key, self::TTL);
        $redis->exec();
    }

    public function clearCpActionInfo($cp_action_id) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_CAI . $cp_action_id;
        return $redis->del($key);
    }

    public function getSchemaCache($store_name) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_SCHEMA . $store_name;
        $value = $redis->get($key);
        if (!$this->debug) {
            $value = gzuncompress($value);
        }
        $unserialized = unserialize($value);
        return $unserialized;
    }

    public function getSchemaCaches($table_names) {
        if (count($table_names) === 0 || $table_names === null) {
            return array();
        }

        $redis = self::getRedis();
        $keys = array();
        foreach ($table_names as $table_name) {
            $key = $this->prefix . self::KEY_SCHEMA . $table_name;
            $keys[] = $key;
        }
        $results = $redis->mget($keys);

        if (count($table_names) !== count($results)) {
            return array();
        }

        $unserialized_results = array();
        for ($i = 0 ; $i < count($results) ; $i ++) {
            $result = $results[$i];
            if (!$this->debug) {
                $result = gzuncompress($result);
            }
            $unserialized = unserialize($result);
            $unserialized_results[$table_names[$i]] = $unserialized;
        }
        return $unserialized_results;
    }

    public function setSchemaCache($store_name, $store) {
        $redis = self::getRedis();
        $key = $this->prefix . self::KEY_SCHEMA . $store_name;
        $serialized = serialize($store);
        if (!$this->debug) {
            $serialized = gzcompress($serialized);
        }
        $redis->watch($key);
        $redis->multi();
        $redis->set($key, $serialized);
        $redis->expire($key, $this->schema_ttl);
        $redis->exec();
    }

    /**
     * @return Redis
     * @throws Exception
     * @throws RedisException
     */
    public static function getRedis() {
        if (CacheManager::$connection === null) {
            CacheManager::$connection = new CacheManager_Connection();
        }

        return CacheManager::$connection->getRedis();
    }
}

/**
 * Class CacheManager_Connection
 *
 * Redisのコネクション管理オブジェクト。
 * CacheManagerとライフサイクルを分けたいので、別オブジェクトとしています。
 */
class CacheManager_Connection {

    /** @var Redis $redis  */
    private $redis = null;

    public function __construct() {
        $host = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.Host');
        $port = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.Port');
        $db_id = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.DbId');

        $this->redis = aafwRedisManager::getRedisInstance($host, $port, $db_id);
    }

    public function __destruct() {
        // クローズされるのは、phpのシャットダウン・シーケンス時のみかつバッチのときのみ。
        // Webのときはセッション書き込み完了後に閉じる必要があるため、何もしません。
        if (php_sapi_name() === 'cli' && $this->redis) {
            $this->redis->close();
        }
    }

    public function getRedis() {
        return $this->redis;
    }
}