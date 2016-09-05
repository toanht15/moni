<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.db.aafwRedisManager');

class MoveConversionFromRedisToDB {
    public $logger;
    public $service_factory;

    public function __construct()
    {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess()
    {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->service_factory->create('ConversionService');
        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        $appConfig = aafwApplicationConfig::getInstance();

        $host = $appConfig->query('@redis.StoreCache.Host');
        $port = $appConfig->query('@redis.StoreCache.Port');
        $dbId = $appConfig->query('@redis.StoreCache.TrackerDbId');

        $redis = aafwRedisManager::getRedisInstance($host, $port, $dbId);

        //redisからコンバージョンデーターを取ってconversion_logsに保存する
        $brands = $brand_service->getAllBrands();
        $redis_conversion = null;
        try{
            foreach ($brands as $brand) {
                $redis_key = 'conversion:brand_'.$brand->id;

                while($redis_conversion = $redis->rPop($redis_key)) {
                    $redis_conversion = json_decode($redis_conversion);
                    $conversion_service->createNewConversionLog($redis_conversion);
                }
            }
        } catch (Exception $e) {
            if ($brand){
                $this->logger->error('MoveConversionFromRedisToDB brand_id='.$brand->id);
            }
            if ($redis_conversion) {
                $this->logger->error($redis_conversion);
            }
            $this->logger->error($e);
        }
    }
}
