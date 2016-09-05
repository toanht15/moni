<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_archive_segment extends BrandcoPOSTActionBase {
    protected $ContainerName = 'segment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $CsrfProtect = true;

    private $target_sid;

    public function doThisFirst() {
        $this->target_sid = $this->POST['target_sid'];
    }

    public function validate() {
        $segment_validator = new SegmentValidator($this->target_sid, $this->getBrand()->id);
        $segment_validator->validate();

        if (!$segment_validator->isValid()) {
            return false;
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function doAction() {
        $segment_service = $this->getService('SegmentService');

        $cur_segment = $segment_service->getSegmentById($this->target_sid);

        $cur_segment->archive_flg = Segment::ARCHIVE_ON;
        $segment_service->updateSegment($cur_segment);

        $json_data = $this->createAjaxResponse("ok", array('s_type' => $cur_segment->type));
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
