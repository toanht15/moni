<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class TwEntriesUsersReply extends aafwEntityBase {
    protected $_Relations = array(
        'TwEntriesUsersMentions' => array(
            'mention_id' => 'id'
        )
    );
}