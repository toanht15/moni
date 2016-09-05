<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class WithdrawReasonsLog extends aafwEntityBase {

    protected $_Relations = array(
        'WithdrawReasonsLogs' => array(
            'id' => 'withdraw_log_id'
        )
    );
}
