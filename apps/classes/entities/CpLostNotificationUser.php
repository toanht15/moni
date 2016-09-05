<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpLostNotificationUser extends aafwEntityBase {
    protected $_Relations = array(
        'CpLostNotification' => array(
            'cp_lost_notification_id' => 'id',
        ),
        'User' => array(
            'user_id' => 'id'
        )
    );
}