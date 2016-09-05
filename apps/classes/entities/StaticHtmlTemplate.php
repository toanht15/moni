<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
class StaticHtmlTemplate extends aafwEntityBase {
    CONST TEMPLATE_TYPE_IMAGE_SLIDER            = 1;
    CONST TEMPLATE_TYPE_FLOAT_IMAGE             = 2;
    CONST TEMPLATE_TYPE_FULL_IMAGE              = 3;
    CONST TEMPLATE_TYPE_TEXT                    = 4;
    CONST TEMPLATE_TYPE_INSTAGRAM               = 5;
    CONST TEMPLATE_TYPE_STAMP_RALLY             = 6;
    CONST TEMPLATE_TYPE_LOGIN_LIMIT_BOUNDARY    = 99;

    public static $template_types = array(
        self::TEMPLATE_TYPE_IMAGE_SLIDER => array('modelName' => 'StaticHtmlImageSliders'),
        self::TEMPLATE_TYPE_FLOAT_IMAGE => array('modelName' => 'StaticHtmlFloatImages'),
        self::TEMPLATE_TYPE_FULL_IMAGE => array('modelName' => 'StaticHtmlFullImages'),
        self::TEMPLATE_TYPE_TEXT => array('modelName' => 'StaticHtmlTextes'),
        self::TEMPLATE_TYPE_INSTAGRAM => array('modelName' => 'StaticHtmlInstagrams'),
        self::TEMPLATE_TYPE_STAMP_RALLY => array('modelName' => 'StaticHtmlStampRallies'),
        self::TEMPLATE_TYPE_LOGIN_LIMIT_BOUNDARY => array('modelName' => ''),
    );
}
