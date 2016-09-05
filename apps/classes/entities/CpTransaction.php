<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpTransaction extends aafwEntityBase {

    protected $_Relations = array(

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),

        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),
    );
}
