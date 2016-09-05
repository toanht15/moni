<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class preview_add_brand_notification extends BrandcoManagerGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        try {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $content = $cache_manager->getCache(CacheManager::BRAND_NOTIFICATION_PREVIEW);
            $this->Data['add_brand_information']['contents'] = $content['contents'];
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        return 'manager/dashboard/preview_add_brand_notification.php';
    }
}