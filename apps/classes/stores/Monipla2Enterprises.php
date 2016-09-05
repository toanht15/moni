<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class Monipla2Enterprises extends aafwEntityStoreBase {

	protected $_TableName = 'enterprise';

	public function __construct( $params = null ) {
		//db_monipla2につなぐための設定
		$params['StoreMaster'] = DBFactory::getInstance()->getDB('monipla2')->Master;
		$params['StoreRead'] = DBFactory::getInstance()->getDB('monipla2')->Read;
		parent::__construct( $params );

	}

}