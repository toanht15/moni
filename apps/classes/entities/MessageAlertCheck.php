<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class MessageAlertCheck extends aafwEntityBase {

    const STATUS_FAIL         = "0"; // 配信失敗
    const STATUS_DELIVERED    = "1"; // 配信済み

    protected $_Relations = array(
        'CpMessageDeliveryReservations' => array(
            'cp_message_delivery_reservation_id' => 'id',
        ),
    );
}