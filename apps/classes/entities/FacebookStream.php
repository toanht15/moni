<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class FacebookStream extends aafwEntityBase {

	public function getType() {
		return StreamService::STREAM_TYPE_FACEBOOK;
	}

	public function getEntryPrefix() {
		return IPanelEntry::ENTRY_PREFIX_FACEBOOK;
	}

	protected $_Relations = array(

		'Brands' => array(
			'brand_id' => 'id',
		),

		'BrandSocialAccounts' => array(
			'brand_social_account_id' => 'id',
		),

		'FacebookEntries' => array(
			'id' => 'stream_id',
		),
	);

	public function getFacebookEntries() {

		return $this->getRelatedObject('FacebookEntries')->find(array('stream_id' => $this->id));
	}
}
