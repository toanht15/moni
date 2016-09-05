<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
class StaticHtmlFloatImage extends aafwEntityBase {
    CONST IMAGE_POSITION_LEFT   = 1;
    CONST IMAGE_POSITION_RIGHT  = 2;

    CONST SP_FLOAT_ON    = 0;
    CONST SP_FLOAT_OFF   = 1;

    public static $image_positions = array(
        self::IMAGE_POSITION_LEFT => '左に配置',
        self::IMAGE_POSITION_RIGHT => '右に配置'
    );
    public static $smartphone_floates = array(
        self::SP_FLOAT_ON => '左右に配置',
        self::SP_FLOAT_OFF => '上下に配置'
    );

}
