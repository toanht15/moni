<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpInstagramHashtagAction extends aafwEntityBase {

    // MAX33件くらいまでしか返ってこない
    const API_PAGE_LIMIT = 50;

    const APPROVAL_ON = 1;
    const APPROVAL_OFF = 0;

    protected $_Relations = array(
        'CpInstagramHashtags' => array(
            'id' => 'cp_instagram_hashtag_action_id'
        )
    );
}
