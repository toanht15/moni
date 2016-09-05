<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpInstantWinAction extends aafwEntityBase {

    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        ),
        'InstantWinPrizes' => array(
            'id' => 'cp_instant_win_action_id',
        )
    );

}