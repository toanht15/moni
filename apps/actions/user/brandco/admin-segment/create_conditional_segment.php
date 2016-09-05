<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SegmentCreateSqlService');

class create_conditional_segment extends BrandcoGETActionBase {
    protected $ContainerName = 'create_conditional_segment';

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    public function doAction() {

        $segment_service = $this->getService('SegmentService');
        $classified_provision = $segment_service->createTmpSegmentProvision();

        $this->Data['default_sps']    = array($classified_provision);

        $this->Data['segment_info'] = array(
            'brand_id' => $this->getBrand()->id,
            'is_active_segment' => false,
            'segment_type' => Segment::TYPE_CONDITIONAL_SEGMENT,
        );

        $this->Data['segment_limit'] = $segment_service->getSegmentLimit(Segment::TYPE_CONDITIONAL_SEGMENT, $this->getBrand()->id);

        return 'user/brandco/admin-segment/conditional_segment.php';
    }
}