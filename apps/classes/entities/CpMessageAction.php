<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpMessageAction extends aafwEntityBase {

    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        ),
    );

}