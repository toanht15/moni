<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class BrandAdministratorAccountInfos extends aafwEntityStoreBase {
    protected $_TableName = "brand_administrator_account_info";
    protected $_EntityName = "BrandAdministratorAccountInfo";

    public static $ACCOUNT_INFO_LIST = array(
        'クライアント担当者',
        'メールアドレス',
        '電話番号',
    );
}
