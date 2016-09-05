<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class InquirySection extends aafwEntityBase {
    // セクションのカテゴリ
    const TYPE_MAJOR    = 1;    // 大
    const TYPE_MEDIUM   = 2;    // 中
    const TYPE_MINOR    = 3;    // 小

    public static $levels = array(
        self::TYPE_MAJOR,
        self::TYPE_MEDIUM,
        self::TYPE_MINOR,
    );

    public static $level_options = array(
        self::TYPE_MAJOR    => '大',
        self::TYPE_MEDIUM   => '中',
        self::TYPE_MINOR    => '小',
    );

    public static function isLevel($level) {
        return isset(self::$level_options[$level]);
    }
}
