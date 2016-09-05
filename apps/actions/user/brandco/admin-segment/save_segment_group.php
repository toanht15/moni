<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_segment_group extends BrandcoPOSTActionBase {
    protected $ContainerName = 'segment_group';
    protected $Form = array(
        'package' => 'admin-segment',
        'action' => 'segment_group/{segment_id}?mid=failed',
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
            $this->ContainerName = 'create_segment_group';
            $this->Form['action'] = 'create_segment_group?mid=failed';
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
                $cur_segment->type = Segment::TYPE_SEGMENT_GROUP;
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
            $segment_provision_order_no = 1;
            $segment_provision_condition_array = $this->POST['spc'];
            $unclassified_segment_provision = array_slice($segment_provision_condition_array, -1, 1, true);
            unset($segment_provision_condition_array[array_keys($unclassified_segment_provision)[0]]);

            // Saving default segment provision
            foreach ($segment_provision_condition_array as $key => $segment_provision_condition) {
                $segment_provision = $segment_service->createEmptyObject();

                $segment_provision->segment_id = $this->segment_id;
                if (!Util::isNullOrEmpty($this->POST['spc_name'][$key])) {
                    $segment_provision->name = $this->POST['spc_name'][$key];
                }
                $segment_provision->order_no = $segment_provision_order_no;
                $segment_provision->provision = $segment_service->getSegmentProvision($segment_provision_condition);
                $segment_provision->type = SegmentProvision::DEFAULT_SEGMENT_PROVISION;

                $segment_service->saveSegmentProvision($segment_provision);

                $segment_provision_order_no++;
            }

            // Saving unconditional segment provision
            if ($this->POST['unconditional_flg'] == Segment::UNCONDITIONAL_SEGMENT_FLG_ON) {
                $segment_provision = $segment_service->createEmptyObject();

                $segment_provision->segment_id = $this->segment_id;
                $segment_provision->name = '未条件セグメント';
                $segment_provision->provision = "";
                $segment_provision->type = SegmentProvision::UNCONDITIONAL_SEGMENT_PROVISION;

                $segment_service->saveSegmentProvision($segment_provision);
            }

            // Saving unclassified segment provision
            if ($this->POST['unclassified_flg'] == Segment::UNCLASSIFIED_SEGMENT_FLG_ON) {
                $segment_provision = $segment_service->createEmptyObject();

                $segment_provision->segment_id = $this->segment_id;
                $segment_provision->name = $this->POST['spc_name'][array_keys($unclassified_segment_provision)[0]];
                $segment_provision->provision = $segment_service->getSegmentProvision(array_values($unclassified_segment_provision)[0]);
                $segment_provision->type = SegmentProvision::UNCLASSIFIED_SEGMENT_PROVISION;

                $segment_service->saveSegmentProvision($segment_provision);
            }

            $segment_transaction->commit();
        } catch (Exception $e) {
            $segment_transaction->rollback();
            return 'redirect: ' . Util::rewriteUrl('admin-segment', 'segment_group', array($this->segment_id), array('mid' => 'failed'));
        }

        if ($cur_segment->status == Segment::STATUS_ACTIVE) {
            return 'redirect: ' . Util::rewriteUrl('admin-segment', 'segment_list', array(), array('mid' => 'action-saved'));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-segment', 'segment_group', array($this->segment_id), array('mid' => 'action-draft'));
    }
}