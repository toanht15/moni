<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandTransaction extends aafwEntityBase {

    protected $_Relations = array(
        'brands' => array(
            'brand_id' => 'id',
        ),
    );
}
