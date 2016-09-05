<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class TwitterStream extends aafwEntityBase {

	public function getType() {
		return StreamService::STREAM_TYPE_TWITTER;
	}

	public function getEntryPrefix() {
		return IPanelEntry::ENTRY_PREFIX_TWITTER;
	}

	protected $_Relations = array(

		'Brands' => array(
			'brand_id' => 'id',
		),

		'BrandSocialAccounts' => array(
			'brand_social_account_id' => 'id',
		),
		'TwitterEntries' => array(
			'id' => 'stream_id',
		),
	);

	public function getTwitterEntries() {

		return $this->getRelatedObject('TwitterEntries')->find(array('stream_id' => $this->id));
	}
}
