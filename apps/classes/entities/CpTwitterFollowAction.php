<?php
/**
 * User: t-yokoyama
 * Date: 15/03/10
 * Time: 13:32
 */

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpTwitterFollowAction extends aafwEntityBase {

    protected $_Relations = array(

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}