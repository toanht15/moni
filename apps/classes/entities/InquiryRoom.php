<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class InquiryRoom extends aafwEntityBase {

    const TYPE_ADMIN    = InquiryMessage::ADMIN;
    const TYPE_MANAGER  = InquiryMessage::MANAGER;

    const STATUS_OPEN           = 1;
    const STATUS_IN_PROGRESS    = 2;
    const STATUS_CLOSED         = 3;
    const STATUS_FORWARDED      = 4;

    /**
     * @var array
     */
    public static $dirs = array(
        self::TYPE_ADMIN    => 'admin-inquiry',
        self::TYPE_MANAGER  => 'inquiry',
    );

    public static $statuses = array(
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_CLOSED,
        self::STATUS_FORWARDED,
    );

    /**
     * @var array
     */
    public static $status_options = array(
        self::STATUS_OPEN           => '未対応',
        self::STATUS_IN_PROGRESS    => '処理中',
        self::STATUS_CLOSED         => '完了',
        self::STATUS_FORWARDED      => '転送済',
    );

    /**
     * @param $type
     * @return bool
     */
    public static function isAdmin($type) {
        return $type == self::TYPE_ADMIN;
    }

    /**
     * @param $type
     * @return bool
     */
    public static function isManager($type) {
        return $type == self::TYPE_MANAGER;
    }

    /**
     * @param $type
     * @return int
     */
    public static function getOppositeType($type) {
        return self::isManager($type) ? self::TYPE_ADMIN :  self::TYPE_MANAGER;
    }

    /**
     * @param $type
     * @return mixed
     */
    public static function getDir($type) {
        if (self::$dirs[$type]) {
            return self::$dirs[$type];
        }

        return self::$dirs[self::TYPE_ADMIN];
    }

    /**
     * @param $status
     * @return bool
     */
    public static function isStatus($status) {
        return isset(self::$status_options[$status]);
    }

    /**
     * @param $status
     * @return null
     */
    public static function getStatus($status) {
        if (self::isStatus($status)) {
            return self::$status_options[$status];
        }

        return null;
    }
}
