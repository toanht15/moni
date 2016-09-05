<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class TweetPhoto extends aafwEntityBase {
    protected $_Relations = array(
        'TweetMessages' => array(
            'tweet_message_id' => 'id',
        ),
    );
}
