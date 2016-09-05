<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class FacebookStreams extends aafwEntityStoreBase {

	protected $_TableName = 'facebook_streams';
	protected $_EntityName = "FacebookStream";

	const POST = 1;
	const NEWS_FEED = 2;

	public static $facebook_stream_kind = array(
		self::POST,
		self::NEWS_FEED,
	);

	public static $facebook_stream_kind_str_array = array(
		self::POST => "post",
		self::NEWS_FEED => "news_feed",
	);

	public static $facebook_timeline_label_array = array(
		0 => 'ニュースフィード',
		1 => '投稿',
	);

	public function getStreamKindName($kind) {
		return self::$facebook_stream_kind_str_array[$kind];
	}

	public function getTimeLineLabel($kind){
		return self::$facebook_timeline_label_array[$kind];
	}
}
