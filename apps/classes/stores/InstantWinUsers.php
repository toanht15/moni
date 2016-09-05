<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class InstantWinUsers extends aafwEntityStoreBase {

    protected $_TableName = 'instant_win_users';
    protected $_EntityName = "InstantWinUser";

    const PRIZE_STATUS_LOSE = 1;
    const PRIZE_STATUS_WIN = 2;
}