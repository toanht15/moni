<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class BrandSocialAccounts extends aafwEntityStoreBase {

    const TOKEN_EXPIRED = 1;
    const TOKEN_NOT_EXPIRE = 0;

    protected $_TableName = 'brand_social_accounts';

}
