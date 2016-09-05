<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class PanelCacheManager {

    private $logger;
    private $service_factory;

    /** @var  BrandService $brand_service */
    private $brand_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->brand_service = $this->service_factory->create('BrandService');
    }

    public function deleteAll() {

        $brands = $this->brand_service->getAllBrands();
        $cache_manager = new CacheManager();

        foreach ($brands as $brand) {

            try {
                $cache_manager->deletePanelCache($brand->id);

            } catch (Exception $e) {
                $this->logger->error('PanelCacheManager Error.' . $e);
            }
        }
    }

    public function deleteByBrandId($brand_id) {
        $brand = $this->brand_service->getBrandById($brand_id);

        if ($brand) {
            $cache_manager = new CacheManager();

            try {
                $cache_manager->deletePanelCache($brand->id);

            } catch (Exception $e) {
                $this->logger->error('PanelCacheManager Error.' . $e);
            }
        }
    }
}
