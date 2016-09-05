<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class MoveRedis {

    private $logger;
    private $service_factory;

    /** @var  NormalPanelService $normal_panel_service */
    private $normal_panel_service;

    /** @var  TopPanelService $top_panel_service */
    private $top_panel_service;

    /** @var  BrandPageSettingService $brand_page_settings_service */
    private $brand_service;

    private $oldRedis;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->normal_panel_service = $this->service_factory->create('NormalPanelService');
        $this->top_panel_service = $this->service_factory->create('TopPanelService');
        $this->brand_service = $this->service_factory->create('BrandService');
        $this->oldRedis = aafwRedisManager::getRedisInstance();
    }

    public function doProcess() {

        $brands = $this->brand_service->getAllBrands();

        foreach ($brands as $brand) {

                try {

                    $top_panel_entries = $this->getTopEntries($brand);

                    $this->top_panel_service->setRedis($this->top_panel_service->createRedis());
                    $top_panel_name = TopPanelService::$panel_name['top'] . $brand->id;

                    foreach($top_panel_entries as $entry_value) {
                        $this->top_panel_service->getRedis()->rPush($top_panel_name, $entry_value);
                    }

                    $normal_panel_entries = $this->getNormalEntries($brand);
                    $normal_panel_name = TopPanelService::$panel_name['normal'] . $brand->id;

                    $this->normal_panel_service->setRedis($this->normal_panel_service->createRedis());


                    foreach($normal_panel_entries as $entry_value) {
                        $this->normal_panel_service->getRedis()->rPush($normal_panel_name, $entry_value);
                    }

                } catch (Exception $e) {
                    $this->logger->error('MoveRedis Error.' . $e);
                }
        }
    }

    private function getNormalEntries($brand) {
        $this->normal_panel_service->setRedis($this->oldRedis);

        $normal_panel_entries = $this->normal_panel_service->getEntriesByPage($brand, 1, 10000);

        return $normal_panel_entries;
    }

    private function getTopEntries($brand) {
        $this->top_panel_service->setRedis($this->oldRedis);
        $top_panel_entries = $this->top_panel_service->getEntriesByPage($brand, 1, 1000);

        return $top_panel_entries;
    }
}