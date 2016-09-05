<?php
/**
 * User: t-yokoyama
 * Date: 15/03/18
 * Time: 13:33
 */

AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CpTwitterFollowLogs extends aafwEntityStoreBase {
    protected $_TableName = 'cp_twitter_follow_logs';
    protected $_EntityName = "CpTwitterFollowLog";
}