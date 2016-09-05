<?php
/**
 * 設定取得クラス
 *
 * @package   aafw
 * @author    allied architechts
 */

class aafwConfig{

  private static $objSelf = null;
  private $values = array (
    'ActionPath'   => '',
    'ParserPath'   => '',
    'TemplatePath' => '',
    'ErrorPage'    => 'views/error_page.php',
    'NotFound'     => 'views/error_page.php',
    'ForBidden'    => 'views/error_page.php',
    'TmpDir'      => '/tmp',
    'PluginDir'    => '__controller_plugin',
    'Debug'        => 0,
    'SubDirectory' => '',
    'SessionTime'  => '1M',
    'SessionType'  => 'PHP',
    'DefaultSessionHandler' => '',
    'MobileSessionHandler'   => '',
    'MobileActionPath'   => '',
    'MobileTemplatePath' => '',
    'SmartActionPath'   => '',
    'SmartTemplatePath' => '',
    'OriginalActionPath'   => '',
    'OriginalTemplatePath' => '',
  );
  public $Parsers = array();

  /**
   *
   * コンストラクタ
   *
   */

  public function __construct(){
    // Paserの読み込み
    $this->ParserPath  = AAFW_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'parsers';
    $d = opendir( $this->ParserPath );
    while( $f = readdir($d) ) {
      if( preg_match( '/^\.+$/',$f ) ) continue;
      if( preg_match( '/^(.+?)Parser\.php$/', $f, $tmp ) ){
        $this->Parsers[$tmp[1]] = array(
          'path'      => $this->ParserPath . '/'. $f ,
          'classname' => $tmp[1] . 'Parser'
        );
      }
    }
    $this->TemplatePath = preg_replace('/\/$/', '', $this->TemplatePath);
    $this->ParserPath   = preg_replace('/\/$/', '', $this->ParserPath);
    $this->ActionPath   = preg_replace('/\/$/', '', $this->ActionPath);

    require_once $this->Parsers['YAML']['path'];
    $c = new $this->Parsers['YAML']['classname'];

    // WEB設定ファイルの読み込み
    $web_yaml_path = DOC_CONFIG . DIRECTORY_SEPARATOR .'web.yml';
    foreach( $c->in($web_yaml_path) as $key => $value ) $this->$key = $value;

    if ( !$this->TemplatePath )       $this->TemplatePath       = AAFW_DIR . DIRECTORY_SEPARATOR . 'views';
    if ( !$this->ActionPath   )       $this->ActionPath         = AAFW_DIR . DIRECTORY_SEPARATOR . 'actions';
    if ( !$this->ModelPath    )       $this->ModelPath          = AAFW_DIR . DIRECTORY_SEPARATOR . 'models';
//    if ( !$this->MobileTemplatePath ) $this->MobileTemplatePath = AAFW_DIR . DIRECTORY_SEPARATOR . 'mobile_views';
//    if ( !$this->MobileActionPath   ) $this->MobileActionPath   = AAFW_DIR . DIRECTORY_SEPARATOR . 'actions';
//    if ( !$this->SmartTemplatePath )  $this->SmartTemplatePath  = AAFW_DIR . DIRECTORY_SEPARATOR . 'smart_views';
//    if ( !$this->SmartActionPath   )  $this->SmartActionPath    = AAFW_DIR . DIRECTORY_SEPARATOR . 'actions';
    foreach ( $this->values as $key => $val ) {
      $this->values[$key] = str_replace ( array ( '${AAFW_DIR}', '${DOC_ROOT}' ), array ( AAFW_DIR, DOC_ROOT ), $val );
    }
  }

  public static function getApplicationConfig(){

    if( self::$objSelf ) return self::$objSelf;
    $class = __CLASS__;
    return self::$objSelf = new $class();

  }

  public function __set( $key, $value ){
    $this->values[$key] = $value;
  }

  public function __get( $key ){
    return $this->values[$key];
  }

  public function getValues(){
    return $this->values;
  }

}
