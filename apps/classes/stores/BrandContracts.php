<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class BrandContracts extends aafwEntityStoreBase {
    protected $_TableName = "brand_contracts";
    protected $_EntityName = "BrandContract";

    const MODE_OPEN = 0; // 公開
    const MODE_CLOSED = 1; // クローズページ表示
    const MODE_SITE_CLOSED = 2; // サイトクローズ
    const MODE_DATA_DELETED = 3; // サイトクローズかつデータ削除済み

    const DATA_MAINTAIN_TERM = "-3 month";
}
