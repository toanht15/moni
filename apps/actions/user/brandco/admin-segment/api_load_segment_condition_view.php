<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SegmentCreateSqlService');
AAFW::import('jp.aainc.classes.entities.SegmentProvision');

class api_load_segment_condition_view extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    public function doAction() {
        $create_sql_service = $this->getService('SegmentCreateSqlService');
        $search_key = $this->target_type;
        if ($this->target_id) {
            $search_key .= '/' . $this->target_id;
        }

        $segment_creator_service = $this->getService('SegmentCreatorService', array($this->getBrand()->id));
        $html = $segment_creator_service->getConditionView($this->category_mode, $this->target_id, $this->target_type);
        $data = array(
            'title' => $create_sql_service->getConditionTitle($search_key, $this->getBrand()->id),
            'target_class' => SegmentCreatorService::getSegmentTargetedClass($this->category_mode)
        );

        $json_data = $this->createAjaxResponse("ok", $data, array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
