<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SegmentService');

class api_load_segment_ads_action extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);

    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    public function doAction() {

        parse_str($this->GET['target_sps'],$segment_provisions);

        $this->setBrandSession(SegmentService::SEGMENT_ADS_CONDITION_SESSION_KEY, $segment_provisions);

        $html = aafwWidgets::getInstance()->loadWidget('SegmentAdsAction')->render(array(
            'segment_provisions' => $segment_provisions,
            'brand_user_relation_id' => $this->getBrandsUsersRelation()->id,
        ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
