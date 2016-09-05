<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.services.PhotoStreamService');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');

class PhotoPostPanelDisplayNum implements IManagerKPI {

    function doExecute($date) {
        $service = new aafwServiceFactory();
        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $service->create('PhotoStreamService');

        $photo_streams = $photo_stream_service->getStreamsByBrandId($this->getBrandIds());

        $photo_stream_id = array();
        foreach ($photo_streams as $photo_stream) {
            $photo_stream_id[] = $photo_stream->id;
        }

        return $photo_stream_service->getAvailableEntriesCount($photo_stream_id);
    }

    private function getBrandIds() {
        $service_factory = new aafwServiceFactory();
        /** @var BrandService $brand_service */
        $brand_service = $service_factory->create('BrandService');
        return $brand_service->getAllPublicBrandIds();
    }
}
