<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpLineAddFriendAction extends aafwEntityBase {

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        )
    );
}
