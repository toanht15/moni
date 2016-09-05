<?php
AAFW::import ( 'jp.aainc.aafwApplicationConfig' );
AAFW::import ( 'jp.aainc.vendor.Net.TokyoTyrant' );
/**
 * TokyoTyprantのシングルインスタンス化
 **/
class aafwTokyoTyrant {
  private static $TT = array();
  public static function getInstance( $type ) {
    if( !self::$TT[$type] ) {
      $app = aafwApplicationConfig::getInstance();
      self::$TT[$type] = new Net_TokyoTyrant();
      self::$TT[$type]->connect(
        $app->TTInfo[$type]['server'],
        $app->TTInfo[$type]['port']
        );
    }
    return self::$TT[$type];
  }
}
