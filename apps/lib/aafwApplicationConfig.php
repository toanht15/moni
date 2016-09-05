<?php
require_once  dirname (__FILE__) . '/parsers/YAMLParser.php';
class aafwApplicationConfig {
  private static $Self = null;
  private $values = array();
  private $Datas  = array();

  public function __set( $key, $value ){ @$this->values[$key] = $value; }
  public function __get( $key )        { return @$this->values[$key];   }
  public function getValues( )         { return $this->values;   }

  public function __construct( $path = ""){
    $this->loadYAML ( $path );
  }

  public function loadYAML ( $path ) {
    if ( $path == "") $path = DOC_CONFIG . DIRECTORY_SEPARATOR .'app.yml';
    if ( preg_match ( '#^@(.+)$#', $path, $tmp ) ) {
      $path = DOC_CONFIG . DIRECTORY_SEPARATOR . $tmp[1] .'.yml';
      if ( !is_file ( $path ) ) throw new aafwException ( 'そんなYAMLはありません' );
      if ( !$this->Datas[$tmp[1]] ) {
        $c = new YAMLParser();
        $this->Datas[$tmp[1]] = $c->in ( $path );
      }
    }
    elseif( is_file(  $path ) ){
      $c = new YAMLParser();
      $this->values = $c->in( $path );
    }
  }

  public function query ( $path ) {
    $path = preg_split ( '#\.#', $path );
    $tmp  = null;
    if ( preg_match ( '#^@(.+)$#', $path[0], $m ) ) {
      $yaml_name = array_shift ( $path );
      $this->loadYAML ( $yaml_name );
      $tmp = $this->Datas[$m[1]];
    }
    else {
      $tmp  = $this->values;
    }
    foreach ( $path as $x ) {
      if ( !$tmp[$x] ) {
        $tmp = null;
        break;
      }
      $tmp = $tmp[$x];
    }
    $tmp = array_walk_deeply ( $tmp, function ( $str ) {
      return str_replace ( array ( '${AAFW_DIR}', '${DOC_ROOT}' ), array ( AAFW_DIR, DOC_ROOT ), $str );
    });
    return $tmp;
  }

  public static function getInstance( $path = "" ){
    if ($path == "") $path = DOC_CONFIG . DIRECTORY_SEPARATOR .'app.yml';
    if( self::$Self ) return self::$Self;
    $class = __CLASS__;
    self::$Self =  new $class( $path );
    return self::$Self;
  }

  public static function reload( $path = '' ){
    $class = __CLASS__;
    self::$Self = new $class( $path );
  }

  /**
   * 余程のことが無ければ使ってはならない
   **/
  public static function  __setInstance( $obj ) {
    self::$Self = $obj;
  }
}
