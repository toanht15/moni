<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_validate_segment_provision_limit extends BrandcoPOSTActionBase {

    protected $ContainerName = 'segment_group';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');

        $is_activatable = $segment_service->isActivatableSegment($this->POST['segment_type'], $this->getBrand()->id);
        $data['modal_id'] = $is_activatable ? 'createSegmentConfirmBox' : 'segmentLimitExceededMsg';

        $json_data = $this->createAjaxResponse('ok', $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
