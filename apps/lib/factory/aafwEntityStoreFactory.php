<?php
AAFW::import ( 'jp.aainc.aafw.db.DBFactory' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwEntityFactory' );
class aafwEntityStoreFactory {
  public static function create ( $class_name ) {
    AAFW::import ( 'jp.aainc.classes.stores.' . $class_name );
    return new $class_name ( array (
      'StoreMaster'   => DBFactory::getInstance()->getDB()->Master,
      'StoreRead'     => DBFactory::getInstance()->getDB()->Read,
      'Config'        => aafwApplicationConfig::getInstance(),
      'EntityFactory' => new aafwEntityFactory(),
    ));
  }
}
