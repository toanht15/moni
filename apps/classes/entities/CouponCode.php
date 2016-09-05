<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class CouponCode extends aafwEntityBase {
    const MAX_NUM_LIMIT = 100000000;

    protected $_Relations = array(
        'Coupons' => array(
            'coupon_id' => 'id'
        )
    );

}
