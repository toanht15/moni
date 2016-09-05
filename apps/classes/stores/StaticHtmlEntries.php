<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlEntries extends aafwEntityStoreBase {

	protected $_TableName = 'static_html_entries';

    CONST SNS_PLUGIN_FACEBOOK = 1;
    CONST SNS_PLUGIN_TWITTER = 2;
    CONST SNS_PLUGIN_GOOGLE = 3;

    CONST DEFAULT_PREVIEW_MODE = 1;
    CONST SESSION_PREVIEW_MODE = 2;

    CONST LAYOUT_NORMAL = 1;
    CONST LAYOUT_LP = 2;
    CONST LAYOUT_FULL = 3;
    CONST LAYOUT_PLAIN = 4;

    CONST WRITE_TYPE_BLOG       = 1;
    CONST WRITE_TYPE_TEMPLATE   = 2;

    public static $sns_plugins = array(
        self::SNS_PLUGIN_FACEBOOK => 'Facebook「いいね！」',
        self::SNS_PLUGIN_TWITTER => 'Twitter「ツイート」',
        self::SNS_PLUGIN_GOOGLE => 'Google+'
    );

    public static $layout_src = array(
        self::LAYOUT_NORMAL => '/img/icon/iconColumn2.png',
        self::LAYOUT_LP => '/img/icon/iconColumn1.png',
        self::LAYOUT_FULL => '/img/icon/iconColumn3.png',
        self::LAYOUT_PLAIN => '/img/icon/iconColumn3.png'
    );

    public static $layout_classes = array(
        self::LAYOUT_NORMAL => 'pageWrap',
        self::LAYOUT_LP => 'lpWrap',
        self::LAYOUT_FULL => 'lpWrap'
    );

    public static $layout_sizes = array(
        self::LAYOUT_NORMAL => '696px',
        self::LAYOUT_LP => '960px',
        self::LAYOUT_FULL => '960px（ヘッダーなし）'
    );

    public static $write_types = array(
        self::WRITE_TYPE_BLOG => 'ブログ形式を利用',
        self::WRITE_TYPE_TEMPLATE => 'テンプレートを利用'
    );
}