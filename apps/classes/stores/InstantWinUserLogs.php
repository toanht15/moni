<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class InstantWinUserLogs extends aafwEntityStoreBase {

    protected $_TableName = 'instant_win_user_logs';
    protected $_EntityName = "InstantWinUserLog";

    const FROM_SMART_PHONE = 1;
    const FROM_PC = 2;
}
