<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CommentPluginShareSetting extends aafwEntityBase {

    public static $comment_plugin_share_settings = array(
        SocialAccount::SOCIAL_MEDIA_FACEBOOK => "Facebook",
        SocialAccount::SOCIAL_MEDIA_TWITTER => "Twitter"
    );
}
