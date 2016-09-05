<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SegmentValidator extends BaseValidator {

    private $brand_id;
    private $segment_id;
    private $service_factory;

    private $segment;

    public function __construct($segment_id, $brand_id = null, $segment = null) {
        parent::__construct();

        $this->segment_id = $segment_id;
        $this->brand_id = $brand_id;
        $this->segment = $segment;
        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {
        if (!$this->isValidSegment()) {
            $this->errors['segment_id'][] = 'セグメントが存在しません';
            return;
        }
    }

    /**
     * @return bool
     */
    public function isValidSegment() {
        if (trim($this->segment_id) === '') {
            return false;
        }

        if ($this->segment === null) {
            $segment_service = $this->service_factory->create('SegmentService');
            $this->segment = $segment_service->getSegmentById($this->segment_id);
        }

        if (!$this->segment->id) {
            return false;
        }

        if ($this->segment->brand_id != $this->brand_id) {
            return false;
        }

        if ($this->segment->archive_flg == Segment::ARCHIVE_ON) {
            return false;
        }

        return true;
    }

    public function isValidSegmentType($type) {
        return $this->segment->type == $type;
    }

    public function getCurSegment() {
        return $this->segment;
    }
}
