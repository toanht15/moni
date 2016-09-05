<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class InquiryMessage extends aafwEntityBase {
    // sender, permissionカラムの値
    const USER = 1;   // ユーザ
    const ADMIN = 2;   // 管理者 (クライアント)
    const MANAGER = 3;   // マネージャー (CS)

    public static $senders = array(
        self::USER => 'ユーザ',
        self::ADMIN => 'クライアント',
        self::MANAGER => 'CS',
    );

    /**
     * @param $sender
     * @return bool
     */
    public static function isSender($sender) {
        return in_array(intval($sender), array(self::USER, self::ADMIN, self::MANAGER));
    }

    /**
     * @param $sender
     * @return bool
     */
    public static function isUser($sender) {
        return $sender == self::USER;
    }

    /**
     * @param $sender
     * @return bool
     */
    public static function isAdmin($sender) {
        return $sender == self::ADMIN;
    }

    /**
     * @param $sender
     * @return null
     */
    public static function getSender($sender) {
        return self::$senders[$sender] ?: null;
    }
}
