<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandSegmentLimit extends aafwEntityBase {

    const SEGMENT_GROUP_DEFAULT_LIMIT       = 1;
    const CONDITIONAL_SEGMENT_DEFAULT_LIMIT = 5;

    public static $segment_default_limit = array(
        Segment::TYPE_SEGMENT_GROUP => self::SEGMENT_GROUP_DEFAULT_LIMIT,
        Segment::TYPE_CONDITIONAL_SEGMENT => self::CONDITIONAL_SEGMENT_DEFAULT_LIMIT
    );
}