<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_duplicate_segment extends BrandcoPOSTActionBase {
    protected $ContainerName = 'segment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $CsrfProtect = true;

    private $target_sid;

    private $segment_service;

    public function doThisFirst() {
        $this->target_sid = $this->POST['target_sid'];
        $this->segment_service = $this->getService('SegmentService');
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
        $segment_transaction = aafwEntityStoreFactory::create('Segments');

        try {
            $segment_transaction->begin();
            $new_segment = $this->segment_service->copySegmentById($this->target_sid);

            $target_sps = $this->segment_service->getSegmentProvisionsBySegmentId($this->target_sid);
            foreach ($target_sps as $sp) {
                $this->segment_service->copySegmentProvision($new_segment->id, $sp);
            }

            $segment_transaction->commit();
                $json_data = $this->createAjaxResponse("ok", array(
                    'redirect_url' => $this->getReturnUrl($new_segment)
                ));
        } catch (Exception $e) {
            $segment_transaction->rollback();
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function getReturnUrl($segment) {
        $action = $segment->type == Segment::TYPE_SEGMENT_GROUP ? 'segment_group' : 'conditional_segment';

        return Util::rewriteUrl('admin-segment', $action, array($segment->id));
    }
}
