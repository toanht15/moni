<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class RetweetPhotoConfig extends aafwEntityBase {
    protected $_Relations = array(
        'CpRetweetActions' => array(
            'cp_retweet_action_id' => 'id',
        ),
    );
}
