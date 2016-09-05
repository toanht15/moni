<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class Monipla2EnterpriseSSOTokens extends aafwEntityStoreBase {

	protected $_TableName = 'enterprise_sso_token';
	protected $_DateCreatedName = 'date_created';
	protected $_DateModifiedName = 'date_updated';

	public function __construct( $params = null ) {
		//db_monipla2につなぐための設定
		$params['StoreMaster'] = DBFactory::getInstance()->getDB('monipla2')->Master;
		$params['StoreRead'] = DBFactory::getInstance()->getDB('monipla2')->Read;
		parent::__construct( $params );

	}

}