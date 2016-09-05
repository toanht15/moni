<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class StaticHtmlExternalPageLoginType extends aafwEntityBase {

    public static $snsLoginTypeOrder = array(
        SocialAccount::SOCIAL_MEDIA_FACEBOOK => "Facebook",
        SocialAccount::SOCIAL_MEDIA_TWITTER => "Twitter",
        SocialAccount::SOCIAL_MEDIA_LINE => "LINE",
        SocialAccount::SOCIAL_MEDIA_INSTAGRAM => "Instagram",
        SocialAccount::SOCIAL_MEDIA_GOOGLE => "Google+",
        SocialAccount::SOCIAL_MEDIA_YAHOO => "Yahoo! JAPAN",
        SocialAccount::SOCIAL_MEDIA_LINKEDIN => "LinkedIn"
    );

    public static $msbcSnsLoginTypeOrder = array(
        SocialAccount::SOCIAL_MEDIA_FACEBOOK => "Facebook",
        SocialAccount::SOCIAL_MEDIA_TWITTER => "Twitter",
        SocialAccount::SOCIAL_MEDIA_LINKEDIN => "LinkedIn",
        SocialAccount::SOCIAL_MEDIA_YAHOO => "Yahoo! JAPAN",
        SocialAccount::SOCIAL_MEDIA_INSTAGRAM => "Instagram",
        SocialAccount::SOCIAL_MEDIA_GOOGLE => "Google+",
        SocialAccount::SOCIAL_MEDIA_LINE => "LINE",
    );
}
