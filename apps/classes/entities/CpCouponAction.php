<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class CpCouponAction extends aafwEntityBase {

    protected $_Relations = array(
        'Coupons' => array(
            'coupon_id' => 'id'
        )
    );
}
