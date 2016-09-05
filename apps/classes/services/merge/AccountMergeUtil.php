<?php

/**
 * アカウントマージに関する処理で静的なものを定義してます
 * Class AccountMergeUtil
 */
class AccountMergeUtil {

    const REDIS_PREFIX = "account_merge";
    const REDIRECT_URL = "authRedirectUrl";
    const ALLIED_ID = "alliedId";
    const REDIS_TTL = 1800;

    /**
     * @param $baseAllied
     * @param $fromAlliedId
     * @param $cpId
     * @param $clientId
     * @return string
     */
    public static function encodeToken($accountMergeSuggestionId, $cpId, $clientId) {
        return base64_encode(
            json_encode(
                array(
                    'account_merge_suggestion_id' => $accountMergeSuggestionId,
                    'cp_id' => $cpId,
                    'client_id' => $clientId
                )
            )
        );
    }

    /**
     * @param $token
     * @return array
     */
    public static function decodeToken($token) {
        $params = json_decode(base64_decode($token), true);
        if( !is_array($params) ) {
            return array();
        }
        return $params;
    }

    public static function setAlliedIdToRedis($alliedId) {
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::ALLIED_ID;
        self::setValueToRedis($key, $alliedId);
    }

    public static function setAuthRedirectUrlToRedis($alliedId, $authRedirectUrl) {
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::REDIRECT_URL;
        self::setValueToRedis($key, $authRedirectUrl);
    }

    private static function setValueToRedis($key, $value) {
        $redis = null;
        try {
            $redis = aafwRedisManager::getRedisInstance();
            $redis->set($key, $value);
            $redis->setTimeout($key, self::REDIS_TTL);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        } finally {
            if( $redis ) {
                $redis->close();
            }
        }
    }

    private static function getValueFromRedis($key) {
        $redis = null;
        try {
            $redis = aafwRedisManager::getRedisInstance();
            return $redis->get($key);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        } finally {
            if( $redis ) {
                $redis->close();
            }
        }
        return null;
    }

    private static function delFromRedis($key) {
        $redis = null;
        try {
            $redis = aafwRedisManager::getRedisInstance();
            $redis->del($key);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        } finally {
            if( $redis ) {
                $redis->close();
            }
        }
    }

    public static function getAlliedIdFromRedis($alliedId) {
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::ALLIED_ID;
        return self::getValueFromRedis($key);
    }

    public static function getAutRedirectUrlFromRedis($alliedId) {
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::REDIRECT_URL;
        return self::getValueFromRedis($key);
    }

    public static function delAuthRedirectUrlFromRedis($alliedId) {
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::REDIRECT_URL;
        self::delFromRedis($key);
    }
    
    public static function delAlliedIdFromRedis($alliedId){
        $key = self::REDIS_PREFIX . ':' . $alliedId . ":" . self::ALLIED_ID;
        self::delFromRedis($key);
    }
}
