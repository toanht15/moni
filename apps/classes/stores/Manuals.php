<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class Manuals extends aafwEntityStoreBase {

    protected $_TableName = 'manuals';
    protected $_EntityName = 'Manual';

    //ファンサイト構築マニュアル
    const CMS = 0;
    //キャンペーン作成マニュアル
    const CAMPAIGN = 1;
}