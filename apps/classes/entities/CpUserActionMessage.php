<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpUserActionMessage extends aafwEntityBase {

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;

    protected $_Relations = array(

        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}