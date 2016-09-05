<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_conditional_segment extends BrandcoPOSTActionBase {
    protected $ContainerName = 'conditional_segment';
    protected $Form = array(
        'package' => 'admin-segment',
        'action' => 'conditional_segment/{segment_id}?mid=failed',
    );

    protected $ValidatorDefinition = array(
        'name' => array(
            'type' => 'str',
            'length' => 255,
            'required' => true
        ),
        'description' => array(
            'type' => 'str',
            'length' => 255
        )
    );

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $segment_id;

    public function doThisFirst() {
        $this->segment_id = $this->POST['segment_id'];

        // For create_segment_group action
        if (Util::isNullOrEmpty($this->segment_id)) {
            $this->ContainerName = 'create_conditional_segment';
            $this->Form['action'] = 'create_conditional_segment?mid=failed';
        }
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        $segment_service = $this->getService('SegmentService');
        $segment_transaction = aafwEntityStoreFactory::create('Segments');
        try {
            $segment_transaction->begin();

            if (Util::isNullOrEmpty($this->segment_id)) {
                $cur_segment = $segment_service->createEmptySegment();

                $cur_segment->brand_id = $this->getBrand()->id;
                $cur_segment->name = $this->POST['name'] ?: 'セグメントテンプレート';
                $cur_segment->status = $this->POST['segment_status'];
                $cur_segment->type = Segment::TYPE_CONDITIONAL_SEGMENT;
                if ($this->POST['description_flg'] == Segment::SEGMENT_DESCRIPTION_FLG_ON) {
                    $cur_segment->description = $this->POST['description'];
                } else {
                    $cur_segment->description = "";
                }

                $cur_segment = $segment_service->updateSegment($cur_segment);
                $this->segment_id = $cur_segment->id;
            } else {
                $cur_segment = $segment_service->getSegmentById($this->segment_id);

                $cur_segment->name = $this->POST['name'] ?: 'セグメントテンプレート';
                if ($this->POST['description_flg'] == Segment::SEGMENT_DESCRIPTION_FLG_ON) {
                    $cur_segment->description = $this->POST['description'];
                } else {
                    $cur_segment->description = "";
                }
                if ($this->POST['segment_status'] != $cur_segment->status) {
                    $cur_segment->status = $this->POST['segment_status'];
                }
                $segment_service->updateSegment($cur_segment);

                $segment_service->deleteSegmentProvisionsBySegmentId($this->segment_id);
            }

            // Saving segment provision data
            $segment_provision = $segment_service->createEmptyObject();
            $segment_provision->order_no = 1;
            $segment_provision->segment_id = $this->segment_id;

            $segment_provision->provision = $segment_service->getSegmentProvision($this->POST['spc'][0]);
            $segment_provision->type = SegmentProvision::DEFAULT_SEGMENT_PROVISION;

            $segment_service->saveSegmentProvision($segment_provision);

            $segment_transaction->commit();
        } catch (Exception $e) {
            $segment_transaction->rollback();
            return 'redirect: ' . Util::rewriteUrl('admin-segment', 'conditional_segment', array($this->segment_id), array('mid' => 'failed'));
        }

        if ($cur_segment->status == Segment::STATUS_ACTIVE) {
            return 'redirect: ' . Util::rewriteUrl('admin-segment', 'segment_list', array(), array('mid' => 'action-saved'));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-segment', 'conditional_segment', array($this->segment_id), array('mid' => 'action-draft'));
    }
}