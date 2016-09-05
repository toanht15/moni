<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SegmentContainerList extends aafwWidgetBase {

    public function doService($params = array()) {
        $segment_service = $this->getService('SegmentService');
        $params['segments'] = $segment_service->getSegmentsByBrandIdAndType($params['brand_id'], $params['s_type']);

        return $params;
    }
}
