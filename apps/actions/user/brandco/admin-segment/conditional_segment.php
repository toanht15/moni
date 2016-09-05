<?php

AAFW::import('jp.aainc.classes.brandco.segment.SegmentActionBase');

class conditional_segment extends SegmentActionBase {

    public function createSegmentData() {

        $this->Data['default_sps'] = $this->default_segment_provisions;

        $this->Data['segment_info'] = array(
            'id' => $this->cur_segment->id,
            'brand_id' => $this->getBrand()->id,
            'is_set_condition' => $this->is_set_condition,
            'is_active_segment' => $this->cur_segment->status == Segment::STATUS_ACTIVE,
            'segment_type' => Segment::TYPE_CONDITIONAL_SEGMENT,
        );

        $this->Data['segment_limit'] = $this->segment_service->getSegmentLimit(Segment::TYPE_CONDITIONAL_SEGMENT, $this->getBrand()->id);
    }

    public function getContainerName() {
        return 'conditional_segment';
    }
    
    public function getSegmentType() {
        return Segment::TYPE_CONDITIONAL_SEGMENT;
    }
}