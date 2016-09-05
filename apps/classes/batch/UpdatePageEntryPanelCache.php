<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

/**
 * Class UpdatePageEntryPanelCache
 * CMSページのパネルをトップページに表示するバッチ
 */
class UpdatePageEntryPanelCache {

    private $logger;
    private $service_factory;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {
        $brand_service = $this->service_factory->create('BrandService');
        $page_stream_service = $this->service_factory->create('PageStreamService');

        $brands = $brand_service->getAllBrands();
        $cache_manager = new CacheManager();

        foreach ($brands as $brand) {
            $cache_del_flg = false;
            $page_stream = $page_stream_service->getStreamByBrandId($brand->id);

            try {
                $params = array();
                if ($page_stream->panel_hidden_flg) {
                    $params['top_hidden_flg'] = 0;
                }

                // 公開設定で時間が公開時間が過ぎている投稿一覧取得
                $page_entries = $page_stream_service->getAvailableEntryByStreamId($page_stream->id, $params);

                if ($page_entries) {
                    foreach ($page_entries as $page_entry) {
                        if ($page_entry->getStaticHtmlEntry()->hidden_flg) continue;

                        // トップページに表示する
                        $page_entry->top_hidden_flg = 0;
                        $page_stream_service->updateEntry($page_entry);

                        $panel_service = $page_entry->priority_flg ? $this->service_factory->create('TopPanelService') : $this->service_factory->create('NormalPanelService');
                        $panel_service->addEntry($brand, $page_entry);
                    }

                    $cache_del_flg = true;
                }

                // 自動掲載の場合はフィルターをかます
                if (!$page_stream->panel_hidden_flg) {
                    $filter_rs = $page_stream_service->filterPanelByLimit($page_stream, $page_stream->display_panel_limit);

                    if ($filter_rs) $cache_del_flg = true;
                }

                $page_entries = $page_stream_service->getHiddenEntryByStreamId($page_stream->id);

                if ($page_entries) {
                    foreach ($page_entries as $page_entry) {
                        $panel_service = $page_entry->priority_flg ? $this->service_factory->create('TopPanelService') : $this->service_factory->create('NormalPanelService');

                        $page_entry->top_hidden_flg = 1;
                        $panel_service->deleteEntry($brand, $page_entry);
                    }

                    $cache_del_flg = true;
                }

                if ($cache_del_flg) {
                    $cache_manager->deletePanelCache($brand->id);
                }
            } catch (Exception $e) {
                $this->logger->error('UpdatePageEntryPanelCache Error.' . $e);
            }
        }
    }
}
