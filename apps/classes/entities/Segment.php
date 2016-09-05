<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class Segment extends aafwEntityBase {

    const SEGMENT_DESCRIPTION_FLG_OFF   = 0;
    const SEGMENT_DESCRIPTION_FLG_ON    = 1;

    const UNCONDITIONAL_SEGMENT_FLG_OFF = 0;
    const UNCONDITIONAL_SEGMENT_FLG_ON  = 1;

    const UNCLASSIFIED_SEGMENT_FLG_OFF  = 0;
    const UNCLASSIFIED_SEGMENT_FLG_ON   = 1;

    // Segment type
    const TYPE_ALL_SEGMENT          = 0;
    const TYPE_CONDITIONAL_SEGMENT  = 1;
    const TYPE_SEGMENT_GROUP        = 2;

    // Segment status
    const STATUS_DRAFT      = 0;
    const STATUS_ACTIVE     = 1;

    // Archive_status
    const ARCHIVE_OFF       = 0;
    const ARCHIVE_ON        = 1;

    const TYPE_CONDITIONAL_SEGMENT_LABEL    = "条件セグメント";
    const TYPE_SEGMENT_GROUP_LABEL          = "セグメントグループ";

    const TYPE_CONDITIONAL_SEGMENT_CLASS    = "labelDynamic";
    const TYPE_SEGMENT_GROUP_CLASS    = "labelRelation";

    public static $segment_type_list = array(
        self::TYPE_CONDITIONAL_SEGMENT,
        self::TYPE_SEGMENT_GROUP
    );

    private $segment_type_label_text = array(
        self::TYPE_CONDITIONAL_SEGMENT => self::TYPE_CONDITIONAL_SEGMENT_LABEL,
        self::TYPE_SEGMENT_GROUP => self::TYPE_SEGMENT_GROUP_LABEL
    );

    private $segment_type_class = array(
        self::TYPE_CONDITIONAL_SEGMENT => self::TYPE_CONDITIONAL_SEGMENT_CLASS,
        self::TYPE_SEGMENT_GROUP => self::TYPE_SEGMENT_GROUP_CLASS
    );

    /**
     * @return mixed
     */
    public function getSegmentTypeLabelText() {
        return $this->segment_type_label_text[$this->type];
    }

    /**
     * @return mixed
     */
    public function getSegmentTypeClass() {
        return $this->segment_type_class[$this->type];
    }

    /**
     * @return bool
     */
    public function isSegmentGroup() {
        return $this->type == self::TYPE_SEGMENT_GROUP;
    }

    /**
     * @return bool
     */
    public function isConditionalSegment() {
        return $this->type == self::TYPE_CONDITIONAL_SEGMENT;
    }

    public function isActive() {
        return $this->status == self::STATUS_ACTIVE;
    }
}