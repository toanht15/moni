<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');
AAFW::import('jp.aainc.classes.services.StreamService');

class SnsPostNum implements IManagerKPI {

    private $sns_post_sum;

    function doExecute($date) {
        foreach (SocialApps::$social_media_page_name_array as $stream_id => $stream_type) {
            $this->sumSnsEntriesCount($stream_type);
        }
        return $this->sns_post_sum;
    }

    private function getBrandIds() {
        $service_factory = new aafwServiceFactory();
        /** @var BrandService $brand_service */
        $brand_service = $service_factory->create('BrandService');
        return $brand_service->getAllPublicBrandIds();
    }

    private function sumSnsEntriesCount($stream_type) {
        /** @var StreamService $stream_service */
        $stream_service = new StreamService($stream_type);
        $streams = $stream_service->getAvailableStreamsByBrandId($this->getBrandIds());

        $stream_ids = array();
        foreach ($streams as $stream) {
            $stream_ids[] = $stream->id;
        }

        if (count($stream_ids)) {
            $this->sns_post_sum += $stream_service->getAllEntriesCountByStreamId($stream_ids);
        }
    }
}
