<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpMessageDeliveryTarget extends aafwEntityBase {

    const STATUS_RESERVED     = "0"; // 配信予約済み
    const STATUS_DELIVERED    = "1"; // 配信済み
    const STATUS_FAIL         = "2"; // 配信失敗
    const FIX_TARGET_ON = "1";  //当選者確定済み
    const FIX_TARGET_OFF = "0"; //当選者未確定

    protected $_Relations = array(
        'CpMessageDeliveryReservations' => array(
            'cp_message_delivery_reservation_id' => 'id',
        ),
    );
}