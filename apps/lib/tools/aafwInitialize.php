<?php
/**
 * よく使うディレクトリ構成を適当に初期化する
 * あるディレクトリは作らないです
 **/
class aafwInitialize {
  public static function showHelp () {
?>
よく使うディレクトリ構成を適当に初期化する
あるディレクトリは作らないです
引数ないです

<?php  }
  /**
   * 短い名前です
   **/
  public static function getShortName () {
    return 'init';
  }

  /**
   *
   **/
  public static function doService ( $argv ){
    //
    //
    //
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/actions' ) )                                mkdir ( AAFW::$AAFW_ROOT . '/actions' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/classes' ) )                                mkdir ( AAFW::$AAFW_ROOT . '/classes' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/classes/entities' ) )                       mkdir ( AAFW::$AAFW_ROOT . '/classes/entities' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/classes/stores' ) )                         mkdir ( AAFW::$AAFW_ROOT . '/classes/stores' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/classes/services' ) )                       mkdir ( AAFW::$AAFW_ROOT . '/classes/services' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/views' ) )                                  mkdir ( AAFW::$AAFW_ROOT . '/views' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/config' ) )                                 mkdir ( AAFW::$AAFW_ROOT . '/config' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/plugins' ) )                                mkdir ( AAFW::$AAFW_ROOT . '/plugins' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/plugins/aciton' ) )                         mkdir ( AAFW::$AAFW_ROOT . '/plugins/action' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/plugins/controller' ) )                     mkdir ( AAFW::$AAFW_ROOT . '/plugins/controller' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/plugins/db_sql' ) )                         mkdir ( AAFW::$AAFW_ROOT . '/plugins/db_sql' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/plugins/validator' ) )                      mkdir ( AAFW::$AAFW_ROOT . '/plugins/validator' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/t' ) )                                      mkdir ( AAFW::$AAFW_ROOT . '/t' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/vendor' ) )                                 mkdir ( AAFW::$AAFW_ROOT . '/vendor' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/widgets' ) )                                mkdir ( AAFW::$AAFW_ROOT . '/widgets' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/widgets/classes' ) )                        mkdir ( AAFW::$AAFW_ROOT . '/widgets/classes' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/widgets/templates' ) )                      mkdir ( AAFW::$AAFW_ROOT . '/widgets/templates' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/../docroot' ) )                             mkdir ( AAFW::$AAFW_ROOT . '/../docroot' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/../docroot/img' ) )                         mkdir ( AAFW::$AAFW_ROOT . '/../docroot/img' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/../docroot/css' ) )                         mkdir ( AAFW::$AAFW_ROOT . '/../docroot/css' );
    if ( !is_dir ( AAFW::$AAFW_ROOT . '/../docroot/js' ) )                          mkdir ( AAFW::$AAFW_ROOT . '/../docroot/js' );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/config/define.php' ) )                     file_put_contents ( AAFW::$AAFW_ROOT . '/config/define.php',                   self::getDefinePHPTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/config/app.yml' ) )                        file_put_contents ( AAFW::$AAFW_ROOT . '/config/app.yml',                      self::getAppYAMLTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/config/web.yml' ) )                        file_put_contents ( AAFW::$AAFW_ROOT . '/config/web.yml',                      self::getWebYAMLTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/config/err.yml' ) )                        file_put_contents ( AAFW::$AAFW_ROOT . '/config/err.yml',                      self::getErrYAMLTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/../docroot/index.php' ) )                  file_put_contents ( AAFW::$AAFW_ROOT . '/../docroot/index.php',                self::getIndexPHPTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/../docroot/.htaccess' ) )                  file_put_contents ( AAFW::$AAFW_ROOT . '/../docroot/.htaccess',                self::getHtAccessTemplate() );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/plugins/controller/URLParameter.php' ) )   file_put_contents ( AAFW::$AAFW_ROOT . '/plugins/controller/URLParameter.php', self::getURLParameter()  );
    if ( !is_file ( AAFW::$AAFW_ROOT . '/plugins/action/DefaultModelSetter.php' ) ) file_put_contents ( AAFW::$AAFW_ROOT . '/plugins/action/DefaultModelSetter.php', self::getDefaultModelSetter()  );
  }

  public static function getDefaultModelSetter () { ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import ( 'jp.aainc.aafw.base.aafwActionPluginBase' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwEntityStoreFactory' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwServiceFactory' );
class DefaultModelSetter extends aafwActionPluginBase {
  protected $HookPoint = 'First';
  protected $Priority  = 1;
  private   $Targets   = array();

  public function doService(){
    foreach ( $this->Action->getModelDefinitions() as $class ) {
      $this->Action->setModel ( $class, aafwEntityStoreFactory::create ( $class ) );
    }
    $this->Action->setServiceFactory ( new aafwServiceFactory () );
    return '';
  }
}
<?php return ob_get_clean(); }
  public static function getDefinePHPTemplate () { ob_start () ?>
<?php print '<?php' . "\n" ?>
error_reporting ( E_ALL - E_NOTICE - E_DEPECATED );
define( 'DEBUG', 1 );
define( 'AAFW_DIR'  , dirname( __FILE__ ) . '/..' );
define( 'DOC_ROOT'  , dirname( __FILE__ ) . '/../../docroot' );
define( 'DOC_CONFIG', dirname( __FILE__ )  );
ini_set( 'include_path',  ini_get('include_path'). PATH_SEPARATOR . AAFW_DIR );
ini_set( 'include_path',  ini_get('include_path'). PATH_SEPARATOR . AAFW_DIR . '/lib' );
ini_set( 'include_path',  ini_get('include_path'). PATH_SEPARATOR . AAFW_DIR . '/lib/base' );
ini_set( 'display_errors', DEBUG );
date_default_timezone_set ( 'Asia/Tokyo' );
require_once 'AAFW.php';
require_once 'aafwFunctions.php';
AAFW::start ();
<?php return ob_get_clean();
  }

  public static function getAppYAMLTemplate () { ob_start () ?>
Protocol:
  Secure: http
  Normal: http
Domain: your.domain.com
DBInfo:
  main:
    w: mysql://user:pass@host/db_name
    r: mysql://user:pass@host/db_name
CacheDir: ${AAFW_DIR}/cache
<?php return ob_get_clean();
  }

  public static function getWebYAMLTemplate () { ob_start () ?>
Debug:1
defaultAction: defaultAction
SessionTime: 0
SubDirectory:
NotFound: error_page.php
ErrorPage: error_page.php
DefaultSessionHandler: aafwDBSessionHandler
MobileSessionHandler:aafwDBSessionHandler
SmartTemplatePath: ${AAFW_DIR}/views
MobileTemplatePath: ${AAFW_DIR}/views
<?php return ob_get_clean();
  }

  public static function getErrYAMLTemplate () { ob_start () ?>
<?php return ob_get_clean();
  }


  public static function getIndexPHPTemplate () { ob_start () ?>
<?php print '<?php' . "\n" ?>
require_once  dirname(__FILE__) . '/../apps/config/define.php';
AAFW::import ( "jp.aainc.aafw.web.aafwController" );
try{
  print aafwController::getInstance()->run();
} catch( Exception $e ) {
  print "Fatal Error!";
  if ( DEBUG ) var_dump( $e );
}
<?php return ob_get_clean();
  }

  public static function getHtAccessTemplate () { ob_start () ?>
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
#RewriteRule . index.html [L]
</IfModule>
<?php return ob_get_clean();
  }

  public static function getURLParameter () { ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import ( 'jp.aainc.aafw.base.aafwControllerPluginBase.php' );
class URLParameter extends aafwControllerPluginBase {
  protected $HookPoint = 'First';
  protected $Priority  = 1;

  public function doService(){
    list( $p, $g, $s, $c, $f, $e, $sv, $r ) = $this->Controller->getParams();
    if ( @$g['action'] )                                                   return ;
    if ( !$sv['REQUEST_URI'] || $sv['REQUEST_URI'] == '/' || preg_match( '#^/\?#', $sv['REQUEST_URI'] ) ){
      $g['action']  = 'index';
      $this->Controller->rewriteParams( $p, $g, $s, $c, $f, $e, $sv, $r );
      return ;
    }
    if ( !preg_match( '#^/([^\?]+)(?:\?|$)#', $sv['REQUEST_URI'], $tmp ) ) return ;
    $subdir  = str_replace( '/', '',  $this->Controller->getSubDirectory() );
    $ac_path = preg_replace ( array( '#//#', '#/$#' ), array( '/', '' ), $this->Controller->getActionPath() );

    list( $package_name, $action_name, $path ) = array( '', '', '' );

    if ( $subdir )  $tmp[1] = preg_replace( '#/?' . $subdir . '/?#', '' , $tmp[1] );
    $path = preg_grep( '#.#', preg_split( '#/#', $tmp[1] ) );
    $tmp  = array();
    foreach( $path as $x ){
      if( preg_match( '#^\.+$#', $x ) ) continue;
      $tmp[] = $x;
    }
    $g['__path'] = $path = $tmp;

    // 該当ファイルがある場合はファイルを優先
    if ( is_file( $ac_path . '/' .  preg_replace( '#\..+$#','', $path[0] ) . '.php' ) ){
      $action_name = array_shift( $path );
    }

    // 該当するディレクトリがある場合
    elseif ( is_dir( $ac_path . '/' . ($this->Controller->getSite() ? $this->Controller->getSite() . '/' : '') . $path[0] ) ) {
      $package_name = array_shift( $path );
      $action_name  = array_shift( $path );
      if ( !$action_name ) $action_name = 'index';
    }
    if ( preg_match( '#^(.+?)\.([^\.]+)$#', $action_name, $tmp ) ){
      $action_name = $tmp[1];
      $req         = $tmp[2];
    }
    elseif ( preg_match( '#^(.+?)\.([^\.]+)#', $path[count($path)-1], $tmp ) ){
      $path[count($path)-1] = $tmp[1];
      $req                  = $tmp[2];
    }
    $g['req']     = $req;
    $g['exts']    = $path;
    $g['action']  = $action_name;
    $g['package'] = $package_name;
    $this->Controller->rewriteParams( $p, $g, $s, $c, $f, $e, $sv, $r );
  }
}
<?php return ob_get_clean();
  }
}
