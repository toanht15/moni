<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class InstantWinPrize extends aafwEntityBase {

    protected $_Relations = array(

        'CpInstantWinAction' => array(
            'cp_instant_win_action_id' => 'id'
        )
    );
}