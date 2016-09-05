<?php
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');

class aafwRedisManager {
	/**
	 * @return Redis
	 * @throws Exception|RedisException
	 */
	public static function getRedisInstance($host = null, $port = null, $db_id = null) {

		try {

			$redis = new Redis();

            if(Util::isNullOrEmpty($host)) {
                $host = aafwApplicationConfig::getInstance()->query('@redis.StoreSession.Host');
            }

            if(Util::isNullOrEmpty($port)) {
                $port = aafwApplicationConfig::getInstance()->query('@redis.StoreSession.Port');
            }

            if(Util::isNullOrEmpty($db_id)) {
                $db_id = aafwApplicationConfig::getInstance()->query('@redis.StoreSession.DbId');
            }

			$redis->connect($host, $port);
			$redis->select($db_id);
			return $redis;
		} catch (RedisException $e) {
			$logger = aafwLog4phpLogger::getDefaultLogger();
			$logger->fatal("aafwRedisManager#getRedisInstance() Redis connect Error");
			$logger->fatal($e);
			throw $e;
		}
	}
}
