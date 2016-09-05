<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpPublicReservation extends aafwEntityBase {

    protected $_Relations = array(

        'Cps' => array(
            'cp_id' => 'id',
        ),
    );
}
