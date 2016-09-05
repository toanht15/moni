<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class RssStream extends aafwEntityBase {

	public function getType() {
		return StreamService::STREAM_TYPE_RSS;
	}

	public function getEntryPrefix() {
		return IPanelEntry::ENTRY_PREFIX_RSS;
	}

	protected $_Relations = array(

		'Brands' => array(
			'brand_id' => 'id',
		),

		'BrandSocialAccounts' => array(
			'brand_social_account_id' => 'id',
		),
		'RssEntries' => array(
			'id' => 'stream_id',
		),
	);

    public function getStreamImage() {
        if($this->image_url) {
            return $this->image_url;
        } else{
            return aafwApplicationConfig::getInstance()->query('Static.Url') . '/img/icon/iconNoImage2.png';
        }
    }

    public function getRssEntries() {
        return $this->getRelatedObject('RssEntries')->find(array('stream_id' => $this->id));
    }
}
