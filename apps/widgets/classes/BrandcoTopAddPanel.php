<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class BrandcoTopAddPanel extends aafwWidgetBase {
    public function doService( $params = array() ){

        $serviceFactory = new aafwServiceFactory();

        /**
         * Hidden Entries
         */
        /** @var BrandGlobalMenuService $brand_global_menu_service */
        $brand_global_menu_service = $serviceFactory->create('BrandGlobalMenuService');
        $params['hiddenEntries'] = $brand_global_menu_service->getAllHiddenEntries($params['brand']->id);

        return $params;
    }
}