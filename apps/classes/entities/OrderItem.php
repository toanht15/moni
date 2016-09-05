<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class OrderItem extends aafwEntityBase {
    protected $_Relations = array(
        'ProductItems' => array(
            'product_item_id' => 'id'
        )
    );
}