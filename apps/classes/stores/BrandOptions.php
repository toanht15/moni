<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class BrandOptions extends aafwEntityStoreBase {
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'brand_options';

    const OPTION_CMS                = 1;
    const OPTION_FAN_LIST           = 2;
    const OPTION_CP                 = 3;
    const OPTION_CRM                = 4;
    const OPTION_HEADER             = 5;
    const OPTION_DASHBOARD          = 7;
    const OPTION_TOP                = 8;
    const OPTION_MYPAGE             = 9;//MYPAGE ∈ CRM
    const OPTION_FACEBOOK_ADS       = 10;
    const OPTION_SEGMENT            = 11;
    const OPTION_COMMENT            = 12;
    const OPTION_TWITTER_ADS        = 13;

    const ON                        = 1;
    const OFF                       = 2;

    public static $OPTION_LIST = array(
        self::OPTION_CP        => 'CP作成',
        self::OPTION_CRM       => 'CRM配信',
        self::OPTION_CMS       => 'CMS作成',
        self::OPTION_FAN_LIST  => 'ファン一覧',
        self::OPTION_DASHBOARD => 'ダッシュボード',
        self::OPTION_TOP       => 'トップページ（SNSパネル等）',
        self::OPTION_HEADER    => 'ヘッダー（企業ロゴ等）',
        self::OPTION_MYPAGE    => 'マイページ（受信BOX）',
        self::OPTION_SEGMENT   => 'セグメント'
    );

    public static $SERVICE_OPTIONS = array(
        BrandContract::PLAN_MANAGER_STANDARD => array(
            self::OPTION_CP        => self::ON,
            self::OPTION_CRM       => self::ON,
            self::OPTION_CMS       => self::ON,
            self::OPTION_FAN_LIST  => self::ON,
            self::OPTION_DASHBOARD => self::ON,
            self::OPTION_TOP       => self::ON,
            self::OPTION_HEADER    => self::ON,
            self::OPTION_MYPAGE    => self::ON,
            self::OPTION_SEGMENT   => self::ON
        ),
        BrandContract::PLAN_MANAGER_CP_LITE => array(
            self::OPTION_CP        => self::ON,
            self::OPTION_CRM       => self::OFF,
            self::OPTION_CMS       => self::OFF,
            self::OPTION_FAN_LIST  => self::OFF,
            self::OPTION_DASHBOARD => self::OFF,
            self::OPTION_TOP       => self::OFF,
            self::OPTION_HEADER    => self::ON,
            self::OPTION_MYPAGE    => self::ON,
            self::OPTION_SEGMENT   => self::OFF
        ),
        BrandContract::PLAN_PROMOTION_BRAND => array(
            self::OPTION_CP        => self::ON,
            self::OPTION_CRM       => self::OFF,
            self::OPTION_CMS       => self::OFF,
            self::OPTION_FAN_LIST  => self::OFF,
            self::OPTION_DASHBOARD => self::OFF,
            self::OPTION_TOP       => self::OFF,
            self::OPTION_HEADER    => self::OFF,
            self::OPTION_MYPAGE    => self::OFF,
            self::OPTION_SEGMENT   => self::OFF
        ),
        BrandContract::PLAN_PROMOTION_MONIPLA => array(
            self::OPTION_CP        => self::ON,
            self::OPTION_CRM       => self::OFF,
            self::OPTION_CMS       => self::OFF,
            self::OPTION_FAN_LIST  => self::OFF,
            self::OPTION_DASHBOARD => self::OFF,
            self::OPTION_TOP       => self::OFF,
            self::OPTION_HEADER    => self::OFF,
            self::OPTION_MYPAGE    => self::OFF,
            self::OPTION_SEGMENT   => self::OFF
        )
    );
}