<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpFreeAnswerActionAnswer extends aafwEntityBase {

    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        ),
    );

}