<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class GiftMessage extends aafwEntityBase{

    const MEDIA_TYPE_FB      = 1;
    const MEDIA_TYPE_LINE    = 2;
    const MEDIA_TYPE_MAIL    = 3;
    const MEDIA_TYPE_DEFAULT = 0;
    const SENT      = 1;
    const NOT_SENT  = 0;

    const PRODUCT_CODE_ID = -1;

    protected $_Relations = array(
        'CpUsers' => array(
            'cp_user_id' => 'id',
        ),
        'CpGiftActions' => array(
            'cp_gift_action_id' => 'id',
        ),
        'CouponCodes' => array(
            'coupon_code_id' => 'id',
        ),
    );
}
