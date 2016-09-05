<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandSalesforce extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        ),
    );
}