<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class Conversion extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        )
    );
}
