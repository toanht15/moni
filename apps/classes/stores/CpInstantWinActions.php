<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CpInstantWinActions extends aafwEntityStoreBase {

    protected $_TableName = 'cp_instant_win_actions';
    protected $_EntityName = "CpInstantWinAction";

    const LOGIC_TYPE_RATE = 1;
    const LOGIC_TYPE_TIME = 2;
    const TIME_MEASUREMENT_MINUTE = 1;
    const TIME_MEASUREMENT_HOUR = 2;
    const TIME_MEASUREMENT_DAY = 3;
    public static $time_measurement_array = array(
        self::TIME_MEASUREMENT_MINUTE => '分おき',
        self::TIME_MEASUREMENT_HOUR => '時間おき',
        self::TIME_MEASUREMENT_DAY => '日',
    );
}