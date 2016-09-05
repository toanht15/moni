<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class CpInstagramHashtagEntry extends aafwEntityBase implements IPanelEntry {

    const TOP_STATUS_AVAILABLE      = 0;
    const TOP_STATUS_HIDDEN         = 1;

    protected $_Relations = array(
        'InstagramHashtagUserPosts' => array(
            'instagram_hashtag_user_post_id' => 'id'
        )
    );

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_CP_INSTAGRAM_HASHTAG;
    }

    public function getStoreName() {
        return "CpInstagramHashtagEntries";
    }

    public function isSocialEntry() {
        return false;
    }

    public function getServicePrefix() {
        return 'CpInstagramHashtagStream';
    }
}
