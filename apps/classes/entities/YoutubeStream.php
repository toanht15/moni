<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class YoutubeStream extends aafwEntityBase {
	const IMG_URL_PREFIX	= 'http://img.youtube.com/vi/';
	const IMG_NAME_MAX_RES	= 'maxresdefualt.jpg';
    const IMG_NAME_SD		= 'sddefault.jpg';
    const IMG_NAME_HQ		= 'hqdefault.jpg';
    const IMG_NAME_MQ		= '0.jpg';

	const EMBED_URL_PREFIX 	= 'https://www.youtube.com/embed/';
	const EMBED_URL_SUFFIX 	= '?rel=0'; // 広告が出なくなる

	public static $img_names = array(
		self::IMG_NAME_MAX_RES, self::IMG_NAME_SD, self::IMG_NAME_HQ, self::IMG_NAME_MQ
	);

	public function getType() {
		return StreamService::STREAM_TYPE_YOUTUBE;
	}

	public function getEntryPrefix() {
		return IPanelEntry::ENTRY_PREFIX_YOUTUBE;
	}

	protected $_Relations = array(

		'Brands' => array(
			'brand_id' => 'id',
		),

		'BrandSocialAccounts' => array(
			'brand_social_account_id' => 'id',
		),
		'YoutubeEntries' => array(
			'id' => 'stream_id',
		),
	);

	public function getYoutubeEntries() {

		return $this->getRelatedObject('YoutubeEntries')->find(array('stream_id' => $this->id));
	}
}
