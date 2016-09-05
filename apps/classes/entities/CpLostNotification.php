<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpLostNotification extends aafwEntityBase {

    const NOTIFIED_SUCCESS = 1;
    const NOTIFIED_FAILED = 0;

    protected $_Relations = array(
        'CpAction' => array(
            'cp_action_id' => 'id',
        ),
    );
}