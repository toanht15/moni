<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_load_segment_container_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);

    protected $AllowContent = array('JSON');

    private $segment_type;

    public function doThisFirst() {
        $this->segment_type = $this->GET['s_type'];
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        $html = aafwWidgets::getInstance()->loadWidget('SegmentContainerList')->render(array(
            'brand_id' => $this->getBrand()->id,
            's_type' => $this->segment_type
        ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
