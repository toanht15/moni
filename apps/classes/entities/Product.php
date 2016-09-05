<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class Product extends aafwEntityBase {
    protected $_Relations = array(
        'ProductItems' => array(
            'id' => 'product_id'
        )
    );
}