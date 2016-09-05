<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class CpRestrictedAddresses extends aafwEntityStoreBase {

    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'cp_restricted_addresses';
    protected $_EntityName = 'CpRestrictedAddress';
}