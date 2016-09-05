<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class PanelDetailWriter {

    private $logger;
    private $service_factory;

    /** @var  NormalPanelService $normal_panel_service */
    private $normal_panel_service;

    /** @var  TopPanelService $top_panel_service */
    private $top_panel_service;

    /** @var  BrandPageSettingService $brand_page_settings_service */
    private $brand_page_settings_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->normal_panel_service = $this->service_factory->create('NormalPanelService');
        $this->top_panel_service = $this->service_factory->create('TopPanelService');
        $this->brand_page_settings_service = $this->service_factory->create('BrandPageSettingService');
    }

    public function doProcess() {

        $page_settings = $this->brand_page_settings_service->getAllPublicPageSettings();


        foreach ($page_settings as $page_setting) {

            try {

                $brand = $page_setting->getBrand();
                $normal_panel_entries = $this->normal_panel_service->getEntriesByPage($brand, 0, NormalPanelService::NORMAL_PANEL_MAX_COUNT);
                $top_panel_entries = $this->top_panel_service->getEntriesByPage($brand, 0, TopPanelService::TOP_PANEL_MAX_COUNT);

                $this->writePanelInfo($brand, $normal_panel_entries, $top_panel_entries);


            } catch (Exception $e) {
                $this->logger->error('PanelDetailWriter Error.' . $e);
            }
        }
    }

    /**
     * @param Brand $brand
     * @param $normal_panel_entries
     * @param $top_panel_entries
     */
    private function writePanelInfo(Brand $brand, $normal_panel_entries, $top_panel_entries) {

        $brand_panel_info = "\n";
        $brand_panel_info .= "########################################" . "\n";
        $brand_panel_info .= "Brand panel info start" . "\n";
        $brand_panel_info .= "brand_id = " . $brand->id . "\n";
        $brand_panel_info .= "normal_panel = " . implode(",", $normal_panel_entries) . "\n";
        $brand_panel_info .= "top_panel = " . implode(",", $top_panel_entries) . "\n";
        $brand_panel_info .= "Brand panel info end" . "\n";
        $brand_panel_info .= "########################################" . "\n";
        $this->logger->info($brand_panel_info);

    }
}
