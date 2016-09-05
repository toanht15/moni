<?php
///
/// Widgetの集合を表現する
///
class aafwWidgets  {
  private $Widgets      = array();
  private static $obj   = array();
  private $BASE_DIR = 'widgets/classes';
  
  public function loadWidget( $name ){
    $path = AAFW_DIR . '/' . $this->BASE_DIR . '/' . $name . '.php';
    if( is_file( $path ) ){
      require_once $path;
      $obj = new $name( );
      return $obj;
    } else {
      throw new Exception( 'ウイジェットがありません' );
    }
  }
  
  ///
  /// シングルトンモデル
  ///
  public static function getInstance(){
    $class = __class__;
    if( !self::$obj ) self::$obj = new $class();
    return self::$obj;
  }
}

