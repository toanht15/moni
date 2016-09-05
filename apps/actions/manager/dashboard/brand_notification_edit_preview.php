<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_notification_edit_preview extends BrandcoManagerGETActionBase {

    protected $AllowContent = array('JSON');

    const PAGE_PREVIEW = 1;

    public function validate() {
        return true;
    }

    private $preview_links = array(
        self::PAGE_PREVIEW => 'preview_update_brand_notification',
    );

    function doAction() {
        try {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $cache_manager->addCacheWithTimeout(CacheManager::BRAND_NOTIFICATION_PREVIEW, json_encode($this->POST));

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);

        }

        $preview_page = Util::rewriteUrl('dashboard', $this->preview_links[$this->POST['preview_type']], array(), array('preview' => '1'), '', true);
        $data = array(
            'preview_url' => $preview_page
        );

        $json_data = $this->createAjaxResponse("ok",$data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
