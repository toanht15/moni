<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CpJoinLimitSnses extends aafwEntityStoreBase {
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'cp_join_limit_sns';
    protected $_EntityName = "CpJoinLimitSns";
}
