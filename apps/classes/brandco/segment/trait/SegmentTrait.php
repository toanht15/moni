<?php

trait SegmentTrait {

    public function createEmptySegment() {
        return $this->segments->createEmptyObject();
    }

    /**
     * @param $segment
     */
    public function updateSegment($segment) {
        return $this->segments->save($segment);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getSegmentsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            )
        );

        return $this->segments->find($filter);
    }

    /**
     * @param $brand_id
     * @param $type
     * @return mixed
     */
    public function getSegmentsByBrandIdAndType($brand_id, $type) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'archive_flg' => Segment::ARCHIVE_OFF
            ),
            'order' => array(
                'name' => 'status',
                'direction' => 'desc'
            )
        );

        if ($type) {
            $filter['conditions']['type'] = $type;
        }

        return $this->segments->find($filter);
    }

    /**
     * @return mixed
     */
    public function getCurrentActiveSegments() {
        $filter = array(
            'conditions' => array(
                'status' => Segment::STATUS_ACTIVE,
                'archive_flg' => Segment::ARCHIVE_OFF
            )
        );

        return $this->segments->find($filter);
    }

    /**
     * @param $segment_id
     * @return mixed
     */
    public function getSegmentById($segment_id) {
        return $this->segments->findOne($segment_id);
    }

    /**
     * @param $type
     * @param $brand_id
     * @return bool
     */
    public function isActivatableSegment($type, $brand_id) {
        $segment_count = $this->countSegmentByType($type, $brand_id);
        $segment_limit = $this->getSegmentLimit($type, $brand_id);

        return $segment_count < $segment_limit;
    }

    /**
     * @param $type
     * @param $brand_id
     * @return mixed
     */
    public function countSegmentByType($type, $brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'type' => $type,
                'status' => Segment::STATUS_ACTIVE,
                'archive_flg' => Segment::ARCHIVE_OFF
            )
        );

        return $this->segments->count($filter);
    }

    /**
     * @param $segment_id
     * @return mixed
     */
    public function copySegmentById($segment_id) {
        $target_segment = $this->getSegmentById($segment_id);

        $new_segment = $this->segments->createEmptyObject();
        $new_segment->brand_id = $target_segment->brand_id;
        $new_segment->name = 'コピー<' . $target_segment->name . '>';
        $new_segment->description = $target_segment->description;
        $new_segment->type = $target_segment->type;
        $new_segment->status = Segment::STATUS_DRAFT;

        return $this->segments->save($new_segment);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getActiveSegmentsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'status' => Segment::STATUS_ACTIVE,
                'archive_flg' => Segment::ARCHIVE_OFF,
                'brand_id' => $brand_id,
            )
        );
        
        return $this->segments->find($filter);
    }
}