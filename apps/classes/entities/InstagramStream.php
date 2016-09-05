<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class InstagramStream extends aafwEntityBase {
    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id',
        ),
        'BrandSocialAccounts' => array(
            'brand_social_account_id' => 'id',
        ),
        'InstagramEntries' => array(
            'id' => 'stream_id',
        ),
    );

    public function getType() {
        return StreamService::STREAM_TYPE_INSTAGRAM;
    }

    public function getEntryPrefix() {
        return IPanelEntry::ENTRY_PREFIX_INSTAGRAM;
    }

    public function getInstagramEntries() {
        return $this->getRelatedObject('InstagramEntries')->find(array('stream_id' => $this->id));
    }
}
