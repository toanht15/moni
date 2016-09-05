<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class CrawlerTypes extends aafwEntityStoreBase {

	protected $_TableName = 'crawler_types';
	protected $_EntityName = "CrawlerType";

	const STREAM_TYPE_TWITTER = 1;
	const STREAM_TYPE_FACEBOOK = 2;
	const STREAM_TYPE_YOUTUBE = 3;
    const STREAM_TYPE_RSS = 4;
    const STREAM_TYPE_INSTAGRAM = 5;

	const CRAWLER_TYPE_TWITTER_USER_TIMELINE_USER_AUTH = "twitter_user_timeline_user_auth";
	const CRAWLER_TYPE_FACEBOOK_USER_POST_USER_AUTH = "facebook_user_post_user_auth";
	const CRAWLER_TYPE_FACEBOOK_UPDATE_DETAIL_USER_AUTH = "facebook_update_detail_user_auth";
	const CRAWLER_TYPE_YOUTUBE_USER_POST_USER_AUTH = "youtube_user_post_user_auth";
    const CRAWLER_RSS = "rss_fetch";
    const CRAWLER_TYPE_INSTAGRAM_USER_RECENT_MEDIA = "instagram_user_recent_media";

	public static $stream_type = array(
		"twitter" => self::STREAM_TYPE_TWITTER,
		"facebook" => self::STREAM_TYPE_FACEBOOK,
		"youtube" => self::STREAM_TYPE_YOUTUBE,
        "rss" => self::STREAM_TYPE_RSS,
        'instagram' => self::STREAM_TYPE_INSTAGRAM
	);

	/**
	 * @return string
	 */
	public static function getCrawlerTwitterTypeName() {
		return self::CRAWLER_TYPE_TWITTER_USER_TIMELINE_USER_AUTH;
	}

	/**
	 * @return string
	 */
	public static function getCrawlerFacebookTypeName() {
		return self::CRAWLER_TYPE_FACEBOOK_USER_POST_USER_AUTH;
	}
	
	/**
	 * @return string
	 */
	public static function getCrawlerYoutubeTypeName() {
		return self::CRAWLER_TYPE_YOUTUBE_USER_POST_USER_AUTH;
	}

    /**
     * @return string
     */
    public static function getCrawlerRssTypeName() {
        return self::CRAWLER_RSS;
    }

    /**
     * @return string
     */
    public static function getCrawlerInstagramTypeName() {
        return self::CRAWLER_TYPE_INSTAGRAM_USER_RECENT_MEDIA;
    }
}
