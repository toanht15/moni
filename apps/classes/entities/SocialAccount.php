<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class SocialAccount extends aafwEntityBase {

    /**
     * SocialAccountServiceにも存在するが、安全に利用するためにentityへ記述追加
     */
    const SOCIAL_MEDIA_PLATFORM  = -1;
    const SOCIAL_MEDIA_FACEBOOK  = 1;
    const SOCIAL_MEDIA_TWITTER   = 3;
    const SOCIAL_MEDIA_GOOGLE    = 4;
    const SOCIAL_MEDIA_YAHOO     = 5;
    const SOCIAL_MEDIA_GDO       = 6;
    const SOCIAL_MEDIA_INSTAGRAM = 7;
    const SOCIAL_MEDIA_LINE      = 8;
    const SOCIAL_MEDIA_LINKEDIN  = 9;

    public static $socialMediaTypeName = array(
        self::SOCIAL_MEDIA_PLATFORM  => 'Platform',
        self::SOCIAL_MEDIA_FACEBOOK  => 'Facebook',
        self::SOCIAL_MEDIA_TWITTER   => 'Twitter',
        self::SOCIAL_MEDIA_GOOGLE    => 'Google',
        self::SOCIAL_MEDIA_YAHOO     => 'Yahoo',
        self::SOCIAL_MEDIA_GDO       => 'Gdo',
        self::SOCIAL_MEDIA_INSTAGRAM => 'Instagram',
        self::SOCIAL_MEDIA_LINE      => 'LINE',
        self::SOCIAL_MEDIA_LINKEDIN  => 'LinkedIn'
    );

    public static $socialMediaTypeIcon = array(
        self::SOCIAL_MEDIA_PLATFORM  => 'PL',
        self::SOCIAL_MEDIA_FACEBOOK  => 'FB',
        self::SOCIAL_MEDIA_TWITTER   => 'TW',
        self::SOCIAL_MEDIA_GOOGLE    => 'GP',
        self::SOCIAL_MEDIA_YAHOO     => 'YH',
        self::SOCIAL_MEDIA_INSTAGRAM => 'IG',
        self::SOCIAL_MEDIA_LINE      => 'LN',
        self::SOCIAL_MEDIA_LINKEDIN  => 'IN'
    );

    public function __toString() {
        return "";
    }
}
