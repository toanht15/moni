<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class BrandGlobalSettings extends aafwEntityStoreBase {
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'brand_global_settings';
}
