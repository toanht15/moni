<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class ClickEmailLinkLog extends aafwEntityBase {

    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id'
        ),
        'CpActions' => array(
            'cp_action_id' => 'id'
        ),
        'Brands' => array(
            'brand_id' => 'id'
        )
    );
}
