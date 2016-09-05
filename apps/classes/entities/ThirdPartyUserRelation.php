<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class ThirdPartyUserRelation extends aafwEntityBase {
    protected $_Relations = array(
        'ThirdPartyMaster' => array(
            'third_party_master_id' => 'id',
        ),
    );
}