<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CodeAuthenticationUser extends aafwEntityBase {

    protected $_Relations = array(
        'CodeAuthenticationCodes' => array(
            'code_auth_code_id' => 'id'
        ),
        'Users' => array(
            'user_id' => 'id'
        ),
        'CpActions' => array(
            'cp_action_id' => id
        )
    );
}