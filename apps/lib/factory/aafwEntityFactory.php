<?php
AAFW::import ( 'jp.aainc.aafw.factory.aafwEntityStoreFactory' );
class aafwEntityFactory {
  private static $_ObjectCache = array ();
  public static function create ( $name )  {
    AAFW::import ( 'jp.aainc.classes.entities.'. $name );
    $obj = new $name ();
    if ( !is_array ( $obj->getRelations () ) )
      throw new aafwException ( 'Relationsの指定が不正です' . get_class ( $obj )  );
    foreach ( $obj->getRelations () as $key => $value )  {
      $related_object = null;
      if ( !isset ( self::$_ObjectCache [$key] ) ) self::$_ObjectCache[$key] = aafwEntityStoreFactory::create ( $key );
      $obj->setRelatedObject ( self::$_ObjectCache[$key] );
    }
    $obj->setConfig ( aafwApplicationConfig::getInstance() );
    return $obj;

  }
}
