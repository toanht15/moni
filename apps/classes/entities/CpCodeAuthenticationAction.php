<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpCodeAuthenticationAction extends aafwEntityBase {

    const CODE_FLG_OFF  = 1;
    const CODE_FLG_ON   = 2;

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id'
        ),
        'CodeAuthentications' => array(
            'code_auth_id' => 'id'
        )
    );
}