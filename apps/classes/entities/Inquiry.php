<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class Inquiry extends aafwEntityBase {

    const TYPE_DEFAULT          = 0;    // デフォルト値 (選択してください)
    const TYPE_REGISTRATION     = 1;    // アカウント登録について
    const TYPE_MODIFICATION     = 2;    // 登録内容変更について
    const TYPE_MAIL_DELIVERY    = 3;    // メール配信について
    const TYPE_CAMPAIGN         = 4;    // キャンペーンについて
    const TYPE_PRESENT          = 5;    // プレゼント賞品について
    const TYPE_FEEDBACK         = 6;    // ご意見・ご要望について
    const TYPE_ERROR            = 7;    // 不具合・エラーについて
    const TYPE_WITHDRAW         = 8;    // 退会について
    const TYPE_OTHERS           = 9;    // その他

    public static $categories = array(
        self::TYPE_REGISTRATION,
        self::TYPE_MODIFICATION,
        self::TYPE_MAIL_DELIVERY,
        self::TYPE_CAMPAIGN,
        self::TYPE_PRESENT,
        self::TYPE_FEEDBACK,
        self::TYPE_ERROR,
        self::TYPE_WITHDRAW,
        self::TYPE_OTHERS,
    );

    public static $category_options = array(
        self::TYPE_DEFAULT          => '選択してください',
        self::TYPE_REGISTRATION     => 'アカウント登録について',
        self::TYPE_MODIFICATION     => '登録内容変更について',
        self::TYPE_MAIL_DELIVERY    => 'メール配信について',
        self::TYPE_CAMPAIGN         => 'キャンペーンについて',
        self::TYPE_PRESENT          => 'プレゼント賞品について',
        self::TYPE_FEEDBACK         => 'ご意見・ご要望について',
        self::TYPE_ERROR            => '不具合・エラーについて',
        self::TYPE_WITHDRAW         => '退会について',
        self::TYPE_OTHERS           => 'その他',
    );

    /**
     * @param $category
     * @return bool
     */
    public static function isCategory($category) {
        return isset(self::$category_options[$category]);
    }

    /**
     * @param $category
     * @return null
     */
    public static function getCategory($category) {
        if (self::isCategory($category)) {
            return self::$category_options[$category];
        }

        return null;
    }
}
