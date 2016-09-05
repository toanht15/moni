<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class CouponCodeUser extends aafwEntityBase {

    protected $_Relations = array(
        'CouponCodes' => array(
            'coupon_code_id' => 'id'
        ),
        'Users' => array(
            'user_id' => 'id'
        ),
        'CpActions' => array(
            'cp_action_id' => 'id'
        )
    );
}
