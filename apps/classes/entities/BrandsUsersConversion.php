<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class BrandsUsersConversion extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        ),
        'Users' => array(
            'user_id' => 'id'
        ),
        'Conversions' => array(
            'conversion_id' => 'id'
        )
    );
}
