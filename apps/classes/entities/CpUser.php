<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpUser extends aafwEntityBase {

    const BEGINNER_USER     = 1;
    const NOT_BEGINNER_USER = 0;

    const DEMOGRAPHY_STATUS_DEFAULT  = 0;
    const DEMOGRAPHY_STATUS_COMPLETE = 1;
    const DEMOGRAPHY_STATUS_NOT_MATCH = 2;

    const NOT_HAVE_ADDRESS = 0;
    const NOT_DUPLICATE_ADDRESS = 1;

    protected $_Relations = array(

        'Cps' => array(
            'cp_id' => 'id',
        ),

        'Users' => array(
            'user_id' => 'id',
        ),
    );

    public function isIncompleteDemography() {
        return $this->demography_flg == self::DEMOGRAPHY_STATUS_DEFAULT;
    }

    public function isNotMatchDemography() {
        return $this->demography_flg == self::DEMOGRAPHY_STATUS_NOT_MATCH;
    }
}
