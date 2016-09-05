<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class CpRestrictedAddress extends aafwEntityBase {

    protected $_Relations = array(
        'Prefectures' => array(
            'pref_id' => 'id'
        )
    );
}
