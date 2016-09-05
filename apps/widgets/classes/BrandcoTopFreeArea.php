<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class BrandcoTopFreeArea extends aafwWidgetBase {
    public function doService($params = array()) {
        if (!$params['freeAreaPreview'] && $params['entry'] = $params['brand']->hasFreeArea()) {

        } elseif ($params['freeAreaPreview']) {
            try {
                $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
                $params['entry']->body = $cache_manager->getCache(CacheManager::FREE_AREA_PREVIEW_KEY, array($params['brand']->id));

            } catch (Exception $e) {
                aafwLog4phpLogger::getDefaultLogger()->error($e);
            }
        }
        return $params;
    }
}
