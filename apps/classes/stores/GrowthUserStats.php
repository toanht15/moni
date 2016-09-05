<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class GrowthUserStats extends aafwEntityStoreBase {

    protected $_TableName = 'growth_user_stats';
    protected $_EntityName = 'GrowthUserStat';
    protected $_DeleteType = self::DELETE_TYPE_PHYSICAL;
}