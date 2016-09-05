<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class BrandLoginSettings extends aafwEntityStoreBase {

    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'brand_login_settings';
    protected $_EntityName = "BrandLoginSetting";
}