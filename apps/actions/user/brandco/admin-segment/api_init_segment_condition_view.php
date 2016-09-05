<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_init_segment_condition_view extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    public function doAction() {
        $params = array(
            'brand_id' => $this->getBrand()->id,
            'is_and_condition' => $this->GET['condition_type'] == 'and',
            'pre_condition_key' => $this->GET['pre_condition_key']
        );

        $html = aafwWidgets::getInstance()->loadWidget('SegmentProvisionConditionSelector')->render($params);

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
