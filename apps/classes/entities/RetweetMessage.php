<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class RetweetMessage extends aafwEntityBase {
    protected $_Relations = array(
        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),
        'CpRetweetActions' => array(
            'cp_retweet_action_id' => 'id',
        ),
    );
}
