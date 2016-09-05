<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class api_fetch_segment_provisions extends BrandcoManagerGETActionBase {

    protected $AllowContent = array('JSON');

    public $NeedManagerLogin = true;

    public function validate() {
        if (!$this->segment_id) {
            return false;
        }

        return true;
    }

    function doAction() {
        $segment_service = $this->getService('SegmentService');
        $segment_provisions = $segment_service->getSegmentProvisionsBySegmentId($this->segment_id);

        $segment_provision_array = array();
        foreach ($segment_provisions as $segment_provision) {
            $segment_provision_array[$segment_provision->id] = $segment_provision->name;
        }

        $response['segment_provisions'] = $segment_provision_array;

        $json_data = $this->createAjaxResponse("ok", $response);
        $this->assign('json_data', $json_data);
        return 'dummy.php';

    }
}