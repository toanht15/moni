<?php
/**
 * User: t-yokoyama
 * Date: 15/03/24
 * Time: 13:32
 */

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpFacebookLikeAction extends aafwEntityBase {

    protected $_Relations = array(

        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}
