<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class SocialApps extends aafwEntityStoreBase {

    protected $_TableName = 'social_apps';
    protected $_EntityName = "SocialApp";

    const PROVIDER_TWITTER = 1;
    const PROVIDER_FACEBOOK = 2;
    const PROVIDER_GOOGLE = 3;
    const PROVIDER_RSS = 4;
    const PROVIDER_INSTAGRAM = 5;

    public static $social_media_provider = array(
        self::PROVIDER_TWITTER,
        self::PROVIDER_FACEBOOK,
        self::PROVIDER_GOOGLE,
        self::PROVIDER_RSS,
        self::PROVIDER_INSTAGRAM
    );

    public static $social_pages = array(
        self::PROVIDER_TWITTER,
        self::PROVIDER_FACEBOOK,
        self::PROVIDER_GOOGLE,
        self::PROVIDER_INSTAGRAM
    );

    public static $social_media_provider_str_array = array(
        self::PROVIDER_TWITTER => "twitter",
        self::PROVIDER_FACEBOOK => "facebook",
        self::PROVIDER_GOOGLE => "google",
        self::PROVIDER_RSS => "rss",
        self::PROVIDER_INSTAGRAM => "instagram"
    );

    public static $social_media_provider_short_str_array = array(
        self::PROVIDER_TWITTER => "TW",
        self::PROVIDER_FACEBOOK => "FB",
        self::PROVIDER_GOOGLE => "GP",
        self::PROVIDER_RSS => "Rss",
        self::PROVIDER_INSTAGRAM => 'IG'
    );

    public static $social_media_page_class_array = array(
        self::PROVIDER_TWITTER      => 'TW',
        self::PROVIDER_FACEBOOK     => 'FB',
        self::PROVIDER_GOOGLE       => 'YT',
        self::PROVIDER_INSTAGRAM    => 'IG'
    );

    public static $social_media_page_name_array = array(
        self::PROVIDER_TWITTER      => 'Twitter',
        self::PROVIDER_FACEBOOK     => 'Facebook',
        self::PROVIDER_GOOGLE       => 'Youtube',
        self::PROVIDER_INSTAGRAM    => 'Instagram'
    );

    public static $social_media_page_og_title = array(
        self::PROVIDER_TWITTER      => 'Twitterアカウント',
        self::PROVIDER_FACEBOOK     => 'Facebookページ',
        self::PROVIDER_GOOGLE       => 'Youtubeチャンネル',
        self::PROVIDER_INSTAGRAM    => 'Instagramギャラリー'
    );

    public static $social_media_page_fan_status = array(
        self::PROVIDER_TWITTER      => 'フォロー済',
        self::PROVIDER_FACEBOOK     => 'いいね！済',
    );

    public static $social_media_page_not_fan_status = array(
        self::PROVIDER_TWITTER      => '未フォロー',
        self::PROVIDER_FACEBOOK     => '未いいね！',
    );

    public static function getSocialMediaProviderName($provider) {
        return self::$social_media_provider_str_array[$provider];
    }

    public static function getSocialMediaProviderShortName($provider) {
        return self::$social_media_provider_short_str_array[$provider];
    }

    public static function getSocialMediaPageClassType($provider) {
        return self::$social_media_page_class_array[$provider];
    }

    public static function getSocialMediaPageName($provider) {
        return self::$social_media_page_name_array[$provider];
    }

    public static function getSocialMediaPageOgTitle($provider) {
        return self::$social_media_page_og_title[$provider];
    }
}