<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class ConversionLogs extends aafwEntityStoreBase {

	protected $_TableName = 'conversion_logs';
	protected $_EntityName = "ConversionLog";

    public function __construct($param) {
        parent::__construct(array (
            'StoreMaster'   => DBFactory::getInstance()->getDB('tracker')->Master,
            'StoreRead'     => DBFactory::getInstance()->getDB('tracker')->Read,
            'Config'        => aafwApplicationConfig::getInstance(),
            'EntityFactory' => new aafwEntityFactory(),
        ));
    }
}
