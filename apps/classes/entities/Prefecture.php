<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class Prefecture extends aafwEntityBase {

    protected $_Relations = array(
        'Regions' => array(
            'region_id' => 'id'
        )
    );

    const PREF_TOKYO = 13;
}
