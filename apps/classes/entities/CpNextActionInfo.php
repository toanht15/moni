<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpNextActionInfo extends aafwEntityBase {

    protected $_Relations = array(

        'CpNextAction' => array(
            'next_action_table_id' => 'id',
        ),
    );

}