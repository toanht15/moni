<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class GiftCouponConfig extends aafwEntityBase {
    protected $_Relations = array(
        'CpGiftActions' => array(
            'cp_gift_action_id' => 'id',
        ),
        'Coupons' => array(
            'coupon_id' => 'id',
        ),
    );
}