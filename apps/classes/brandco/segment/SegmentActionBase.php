<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.services.SegmentCreateSqlService');
AAFW::import('jp.aainc.classes.validator.SegmentValidator');

abstract class SegmentActionBase extends BrandcoGETActionBase {

    protected $ContainerName = '';

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;

    protected $cur_segment;
    protected $cur_segment_id;

    protected $segment_service = null;
    protected $default_segment_provisions = null;
    protected $is_set_condition = false;

    public function doThisFirst() {
        $this->ContainerName = $this->getContainerName();
        $this->cur_segment_id = $this->GET['exts'][0];
        $this->deleteErrorSession();
    }

    public function validate() {
        $segment_validator = new SegmentValidator($this->cur_segment_id, $this->getBrand()->id);
        $segment_validator->validate();

        if (!$segment_validator->isValid()) {
            return '404';
        }

        if(!$segment_validator->isValidSegmentType($this->getSegmentType())) {
            return '404';
        }

        $this->cur_segment = $segment_validator->getCurSegment();

        return true;
    }

    public function doAction() {
        $this->segment_service = $this->getService('SegmentService');

        $this->default_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($this->cur_segment_id, SegmentProvision::DEFAULT_SEGMENT_PROVISION);

        foreach ($this->default_segment_provisions as $default_segment_provision) {
            if (!Util::isNullOrEmpty($default_segment_provision->provision)) {
                $this->is_set_condition = true;
                break;
            }
        }

        $this->createSegmentData();

        if (!$this->getActionContainer('Errors')) {
            $cur_segment = $this->cur_segment->toArray();
            $cur_segment['description_flg'] = !Util::isNullOrEmpty($cur_segment['description']) ? 1 : 0;
            $this->assign('ActionForm', $cur_segment);
        }

        return  $this->getReturnUrl();
    }

    abstract public function createSegmentData();

    abstract public function getContainerName();

    abstract public function getSegmentType();

    private function getReturnUrl() {

        if($this->cur_segment->type == Segment::TYPE_SEGMENT_GROUP) {
            return 'user/brandco/admin-segment/segment_group.php';
        }

        return 'user/brandco/admin-segment/conditional_segment.php';
    }
}