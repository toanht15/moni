<?php
AAFW::import('jp.aainc.classes.brandco.segment.SegmentActionBase');

class segment_group extends SegmentActionBase {

    public function createSegmentData() {

        $this->Data['default_sps'] = $this->default_segment_provisions;

        $unconditional_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($this->cur_segment_id, SegmentProvision::UNCONDITIONAL_SEGMENT_PROVISION);
        if ($unconditional_segment_provisions) {
            $this->Data['unconditional_flg'] = Segment::UNCONDITIONAL_SEGMENT_FLG_ON;
        }

        $unclassified_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($this->cur_segment_id, SegmentProvision::UNCLASSIFIED_SEGMENT_PROVISION);
        if ($unclassified_segment_provisions) {
            $this->Data['unclassified_flg'] = Segment::UNCLASSIFIED_SEGMENT_FLG_ON;
            $this->Data['unclassified_sp'] = $unclassified_segment_provisions->current();
        }

        $this->Data['segment_info'] = array(
            'id' => $this->cur_segment->id,
            'brand_id' => $this->getBrand()->id,
            'is_set_condition' => $this->is_set_condition,
            'is_active_segment' => $this->cur_segment->status == Segment::STATUS_ACTIVE,
            'segment_type' => Segment::TYPE_SEGMENT_GROUP,
        );

        $this->Data['segment_limit'] = $this->segment_service->getSegmentLimit(Segment::TYPE_SEGMENT_GROUP, $this->getBrand()->id);
    }

    public function getContainerName() {
        return 'segment_group';
    }

    public function getSegmentType() {
        return Segment::TYPE_SEGMENT_GROUP;
    }
}