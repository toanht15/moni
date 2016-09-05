<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpGiftAction extends aafwEntityBase {

    const INCENTIVE_TYPE_COUPON = 1;
    const INCENTIVE_TYPE_PRODUCT = 2;

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}