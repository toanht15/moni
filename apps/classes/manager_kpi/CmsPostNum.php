<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');
AAFW::import('jp.aainc.classes.services.StaticHtmlEntryService');

class CmsPostNum implements IManagerKPI {

    function doExecute($date) {
        $service_factory = new aafwServiceFactory();
        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $service_factory->create('StaticHtmlEntryService');
        return $static_html_entry_service->countPublicEntryByBrandId($this->getBrandIds());
    }

    private function getBrandIds() {
        $service_factory = new aafwServiceFactory();
        /** @var BrandService $brand_service */
        $brand_service = $service_factory->create('BrandService');
        return $brand_service->getAllPublicBrandIds();
    }
}
