<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandBusinessCategory extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        ),
    );

    //業種
    const CATEGORY_MAKER_FOOD                 = 1;
    const CATEGORY_MAKER_COSMETIC             = 2;
    const CATEGORY_MAKER_CHEMICAL             = 3;
    const CATEGORY_MAKER_ELECTRIC_APPLIANCE   = 4;
    const CATEGORY_MAKER_MACHINE              = 5;
    const CATEGORY_MAKER_AUTOMOBILE           = 6;
    const CATEGORY_MAKER_APPAREL              = 7;
    const CATEGORY_MAKER_INTERIOR             = 8;
    const CATEGORY_MAKER_OTHERS               = 9;
    const CATEGORY_RETAIL_DEPARTMENT          = 10;
    const CATEGORY_RETAIL_ELECTRIC_APPLIANCE  = 11;
    const CATEGORY_RETAIL_APPAREL             = 12;
    const CATEGORY_RETAIL_CVS                 = 13;
    const CATEGORY_RETAIL_OTHERS              = 14;
    const CATEGORY_INTERNET_BUSINESS          = 15;
    const CATEGORY_FINANCE                    = 16;
    const CATEGORY_PUBLISHING                 = 17;
    const CATEGORY_WEB_PRODUCTION             = 18;
    const CATEGORY_MARKETING                  = 19;
    const CATEGORY_TELECOMMUNICATIONS         = 20;
    const CATEGORY_LEISURE                    = 21;
    const CATEGORY_RECRUITING                 = 22;
    const CATEGORY_EDUCATION                  = 23;
    const CATEGORY_MEDICAL                    = 24;
    const CATEGORY_FOOD_SERVICE               = 25;
    const CATEGORY_TRADING_FIRM               = 26;
    const CATEGORY_TRANSPORT                  = 27;
    const CATEGORY_CONSTRUCTION               = 28;
    const CATEGORY_UTILITY                    = 29;
    const CATEGORY_AGRICULTURE                = 30;
    const CATEGORY_INDUSTRIAL                 = 31;
    const CATEGORY_NPO                        = 32;
    const CATEGORY_OTHERS                     = 33;

    //規模
    const SIZE_ULTRA_LARGE = 1;
    const SIZE_LARGE       = 2;
    const SIZE_MEDIUM      = 3;
    const SIZE_SMALL       = 4;
    
    public static $brand_business_category_list = array(
        self::CATEGORY_MAKER_FOOD                => 'メーカー(食品・飲料・健康食品)',
        self::CATEGORY_MAKER_COSMETIC            => 'メーカー（化粧品・美容・コスメ）',
        self::CATEGORY_MAKER_CHEMICAL            => 'メーカー（化学・医薬品）',
        self::CATEGORY_MAKER_ELECTRIC_APPLIANCE  => 'メーカー（家電・パソコン）',
        self::CATEGORY_MAKER_MACHINE             => 'メーカー（電気・精密機器）',
        self::CATEGORY_MAKER_AUTOMOBILE          => 'メーカー(自動車)',
        self::CATEGORY_MAKER_APPAREL             => 'メーカー（アパレル・スポーツ用品）',
        self::CATEGORY_MAKER_INTERIOR            => 'メーカー（インテリア・雑貨）',
        self::CATEGORY_MAKER_OTHERS              => 'メーカー（その他）',
        self::CATEGORY_RETAIL_DEPARTMENT         => '小売（百貨店）',
        self::CATEGORY_RETAIL_ELECTRIC_APPLIANCE => '小売（家電）',
        self::CATEGORY_RETAIL_APPAREL            => '小売（アパレル）',
        self::CATEGORY_RETAIL_CVS                => '小売（CVS）',
        self::CATEGORY_RETAIL_OTHERS             => '小売（その他）',
        self::CATEGORY_INTERNET_BUSINESS         => 'インターネットサービス・ネットメディア',
        self::CATEGORY_FINANCE                   => '金融・証券・保険',
        self::CATEGORY_PUBLISHING                => '出版・印刷・放送',
        self::CATEGORY_WEB_PRODUCTION            => 'Web制作・SEO',
        self::CATEGORY_MARKETING                 => '広告・PR・マーケティング',
        self::CATEGORY_TELECOMMUNICATIONS        => '通信',
        self::CATEGORY_LEISURE                   => 'レジャー',
        self::CATEGORY_RECRUITING                => '人材紹介・ヘッドハンティング・リクルーティング',
        self::CATEGORY_EDUCATION                 => '教育・学習・趣味',
        self::CATEGORY_MEDICAL                   => '医療機関・福祉',
        self::CATEGORY_FOOD_SERVICE              => '飲食店（外食）',
        self::CATEGORY_TRADING_FIRM              => '商社',
        self::CATEGORY_TRANSPORT                 => '交通・輸送',
        self::CATEGORY_CONSTRUCTION              => '建設・不動産',
        self::CATEGORY_UTILITY                   => '水・ガス・電気・石油',
        self::CATEGORY_AGRICULTURE               => '農林・水産',
        self::CATEGORY_INDUSTRIAL                => '工業',
        self::CATEGORY_NPO                       => '非営利法人・官公庁',
        self::CATEGORY_OTHERS                    => 'その他サービス',
    );

    public static $brand_business_size_list = array(
        self::SIZE_ULTRA_LARGE => '超大',
        self::SIZE_LARGE       => '大',
        self::SIZE_MEDIUM      => '中',
        self::SIZE_SMALL       => '小',
    );
}