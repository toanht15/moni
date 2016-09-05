<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class OpenEmailTrackingLog extends aafwEntityBase {

    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id'
        ),
        'CpActions' => array(
            'cp_action_id' => 'id'
        )
    );
}
