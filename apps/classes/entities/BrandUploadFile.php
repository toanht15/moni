<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandUploadFile extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        ),
        'UploadFiles' => array(
            'file_id' => 'id'
        )
    );
    
    const POPUP_FROM_SETTING_MENU                       = 1;
    const POPUP_FROM_STATIC_HTML_ENTRY                  = 2;
    const POPUP_FROM_PHOTO_MODULE                       = 3;
    const POPUP_FROM_TEXT_MODULE                        = 4;
    const POPUP_FROM_INSTANT_LOSE_SETTING               = 5;
    const POPUP_FROM_INSTANT_WIN_SETTING                = 6;
    const POPUP_FROM_INSTAGRAM_HASHTAG                  = 7;
    const POPUP_FROM_GIFT_INCENTIVE_SETTING             = 8;
    const POPUP_FROM_POPULAR_VOTE_MODULE                = 9;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_SLIDER  = 10;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FLOAT   = 11;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FULL    = 12;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_JOINED    = 13;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_FINISH    = 14;
    const POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_PREPARE    = 15;
    const POPUP_FROM_MOVIE_MODULE                       = 16;

    const CP_STATUS_IMAGE = 1;
    const CP_STATUS_IMAGE_WIDTH = 1000;
    const CP_STATUS_IMAGE_HEIGHT = 524;

    private static $allow_access_popup = array(
        self::POPUP_FROM_SETTING_MENU,
        self::POPUP_FROM_STATIC_HTML_ENTRY,
        self::POPUP_FROM_PHOTO_MODULE,
        self::POPUP_FROM_MOVIE_MODULE,
        self::POPUP_FROM_TEXT_MODULE,
        self::POPUP_FROM_INSTANT_LOSE_SETTING,
        self::POPUP_FROM_INSTANT_WIN_SETTING,
        self::POPUP_FROM_INSTAGRAM_HASHTAG,
        self::POPUP_FROM_GIFT_INCENTIVE_SETTING,
        self::POPUP_FROM_POPULAR_VOTE_MODULE,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_SLIDER,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FLOAT,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FULL,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_JOINED,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_FINISH,
        self::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_PREPARE
    );

    /**
     * @param $popup_access_mode
     * @return bool
     */
    public static function isPopupAccessible($popup_access_mode) {
        return in_array($popup_access_mode, self::$allow_access_popup);
    }
}