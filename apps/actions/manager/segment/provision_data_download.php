<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class provision_data_download extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');
        $active_segments = $segment_service->getCurrentActiveSegments();

        $active_segment_array['0'] = '未選択';
        foreach ($active_segments as $active_segment) {
            $active_segment_array[$active_segment->id] = $active_segment->name;
        }

        $this->Data['active_segments'] = $active_segment_array;
        $this->Data['segment_provisions'] = array('0' => '未選択');

        return 'manager/segment/provision_data_download.php';
    }
}
