<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class CodeAuthenticationCode extends aafwEntityBase {
    const MAX_NUM_LIMIT = 100000000;

    protected $_Relations = array(
        'CodeAuthentications' => array(
            'code_auth_id' => 'id'
        )
    );

}
