<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class TwitterStreams extends aafwEntityStoreBase {

	protected $_TableName = 'twitter_streams';
	protected $_EntityName = "TwitterStream";

	const TIMELINE = 1;

	const TIMELINE_HOME = 0;
	const TIMELINE_USER = 1;
	const TIMELINE_MENTION = 2;
	const TIMELINE_FAVORITE = 3;

	public static $twitter_kind = array(
		self::TIMELINE,
	);

	public static $twitter_kind_label_array = array(
		self::TIMELINE => "タイムライン",
	);

	public static $twitter_timeline_label_array = array(
		0 => 'ホーム',
		1 => 'ユーザー',
		2 => 'メンション',
		3 => 'お気に入り',
	);

	public static function getKindLabel($kind) {
		return self::$twitter_kind_label_array[$kind];
	}

	public static function getTimeLineLabel($label) {
		return self::$twitter_timeline_label_array[$label];
	}
	
}
