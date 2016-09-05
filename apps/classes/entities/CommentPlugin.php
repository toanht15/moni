<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CommentPlugin extends aafwEntityBase {

    const COMMENT_PLUGIN_TYPE_ALL       = "0";
    const COMMENT_PLUGIN_TYPE_INTERNAL  = "1";
    const COMMENT_PLUGIN_TYPE_EXTERNAL  = "2";

    const COMMENT_PLUGIN_STATUS_PRIVATE     = 1;
    const COMMENT_PLUGIN_STATUS_PUBLIC      = 2;

    const COMMENT_PLUGIN_ACTIVE_FLG_OFF = '0';
    const COMMENT_PLUGIN_ACTIVE_FLG_ON  = '1';

    const COMMENT_PLUGIN_LOGIN_LIMIT_FLG_OFF    = '0';
    const COMMENT_PLUGIN_LOGIN_LIMIT_FLG_ON     = '1';

    const ORDER_TYPE_ASC    = 1;
    const ORDER_TYPE_DESC   = 2;

    const PLUGIN_CODE_PREFIX = 'moni-cmt-plugin-';

    public static $comment_plugin_status_label = array(
        self::COMMENT_PLUGIN_STATUS_PRIVATE => 'linkNonDisplay',
        self::COMMENT_PLUGIN_STATUS_PUBLIC  => 'iconCheck3'
    );

    public static $comment_plugin_login_limit = array(
        self::COMMENT_PLUGIN_LOGIN_LIMIT_FLG_ON     => '必須',
        self::COMMENT_PLUGIN_LOGIN_LIMIT_FLG_OFF    => '任意'
    );

    public static $comment_plugin_status_options = array(
        self::COMMENT_PLUGIN_STATUS_PUBLIC  => '表示（標準）',
        self::COMMENT_PLUGIN_STATUS_PRIVATE => '非表示'
    );

    public static $comment_plugin_type_options = array(
        self::COMMENT_PLUGIN_TYPE_ALL       => '全て',
        self::COMMENT_PLUGIN_TYPE_INTERNAL  => 'ページ',
        self::COMMENT_PLUGIN_TYPE_EXTERNAL  => '外部埋め込み'
    );

    public static $order_type_label = array(
        self::ORDER_TYPE_DESC => '更新日（降順）',
        self::ORDER_TYPE_ASC => '更新日（昇順）'
    );

    public static $order_types = array(
        self::ORDER_TYPE_DESC => 'desc',
        self::ORDER_TYPE_ASC => 'asc'
    );

    /**
     * @return bool
     */
    public function isPublic() {
        if ($this->status == self::COMMENT_PLUGIN_STATUS_PRIVATE) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isActive() {
        if ($this->active_flg == self::COMMENT_PLUGIN_ACTIVE_FLG_OFF) {
            return false;
        }

        return true;
    }

    /**
     * @param $brand_id
     * @return bool
     */
    public function isLegalBrand($brand_id) {
        if ($this->brand_id != $brand_id) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isEmbedPlugin() {
        if ($this->type != self::COMMENT_PLUGIN_TYPE_EXTERNAL) {
            return false;
        }

        return true;
    }
}
