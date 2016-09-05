<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class BrandUserAttributeDefinitions extends aafwEntityStoreBase {

    protected $_TableName = 'brand_user_attribute_definitions';

    protected $_EntityName = "BrandUserAttributeDefinition";

    const ATTRIBUTE_TYPE_REAL_NUMBER = "0";

    /**
     * value_setカラムにJSON形式で以下のようにキーと値のペアを定義することで、
     * 画面に表示するラベルを変更できます。
     * 例: {"A": "あ", "B": "い", "C": "う", "D": "え"}
     */
    const ATTRIBUTE_TYPE_SET = "1";

    const ATTRIBUTE_TYPE_DATE = "2";

    const ATTRIBUTE_TYPE_TEXT = "3";
}