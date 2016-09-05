<?php
///
/// 抽象的なWidget
///
AAFW::import ( 'jp.aainc.aafw.parsers.PHPParser' );
abstract class aafwWidgetBase extends PHPParser {
  private $BASE_DIR = 'widgets/templates';
  protected $values = array();
  public function render( $params = array() ){
    $data = $this->doService( $params );
    $this->values = $data;
    ob_start();
    include(  AAFW_DIR . '/' . $this->BASE_DIR . '/' . get_class( $this ) . '.php' );
    return ob_get_clean();
  }

  public function doService( $params = array() ) {
    return $params;
  }
}
