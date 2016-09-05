<?php
require_once dirname (__FILE__) . '/aafwFunctions.php';
require_once dirname (__FILE__) . '/aafwApplicationConfig.php';
class AAFW {
  public static $AAFW_ROOT  = '';

  /**
   * フレームワークスタート
   */
  public static function start ( $config = null ) {
    if ( !self::$AAFW_ROOT ) self::$AAFW_ROOT = realpath ( dirname( __FILE__ ) . '/..' );
    if     ( $config  ) require_once $config;
    elseif ( is_file ( self::$AAFW_ROOT . '/config/define.php' ) ) require_once self::$AAFW_ROOT . '/config/define.php';
    define ( 'AAFW_DIR', self::$AAFW_ROOT );
    define ( 'DOC_CONFIG', self::$AAFW_ROOT . '/config' );

    if (php_sapi_name() !== 'cli') {
        ini_set("session.cookie_domain", "." . Util::getMappedServerName());
    }
  }

  /**
   * クラスをとりこむ
   */
  public static function import ( $class_path ) {
      if( !AAFW::$AAFW_ROOT ) AAFW::$AAFW_ROOT  = realpath ( dirname( __FILE__ ). '/..' );
    $target = self::getTargets( $class_path );
    $result = array();
    foreach( $target as $class ){
      if( preg_match( '#\.php$#', $class ) ){
        if( !is_file( $class ) ) throw new Exception( 'not found' . $class );
        $class_name = preg_replace( '#\.php$#', '', basename( $class ) );
        if ( !class_exists ( $class_name, false ) && !interface_exists ( $class_name, false ) ) require_once $class;
        $result[] = $class_name;
      } else {
        $result[] = $class;
     }
    }
    return $result;
  }

  /**
   *
   */
  public static function config ( $q ) {
    $conf = aafwApplicationConfig::getInstance();
    return $conf->query ( $q );
  }

  /***********************
   * aafwのパス表記をファイルシステムのフルパスで返す
   * @param aafwのパス表記
   * @return ファイルシステムでのフルパス(配列)
   ***********************/
  public static function getTargets( $path ){
    $target = array();
    $path = self::toFileSystemPath( $path );
    if( preg_match( '#/\*$#', $path ) ){
      $dir =  preg_replace( '#\*$#', '', $path );
      if( is_dir( $dir ) ){
        $d = opendir( $dir );
        while( $fn = readdir( $d ) ){
          if( !preg_match( '#\.(?:php|sql)$#', $fn ) ) continue;
          $target[] = realpath ( $dir . '/' .  $fn );
        }
      }
    } else {
      $target[] = realpath ( $path . '.php' );
    }
    return  $target;
  }

  /**
   * AAFWのパスからファイルシステムのパスに変換する
   * @param AAFW表記のパス
   * @return ファイルシステムのパス
   */
  public static function toFileSystemPath ( $class_path ) {
    $path = '';
    if     ( preg_match ( '#^jp.aainc.aafw\.#', $class_path ) ) $path = self::$AAFW_ROOT . '/lib/' . str_replace ( '.', '/', preg_replace ( '#^jp\.aainc\.aafw\.#', '', $class_path ) );
    elseif ( preg_match ( '#^jp.aainc.#'      , $class_path ) ) $path = self::$AAFW_ROOT . '/'     . str_replace ( '.', '/', preg_replace ( '#^jp\.aainc#',         '', $class_path ) );
    return  $path;
  }

  /*******************************
   * ダンプする
   * @param オブジェクトまたは配列
   * @return <ul><li>の形の文字列</li></ul>
   *******************************/
  public static function dump( $var ){
    if( DEBUG )                    return ;
    if( php_sapi_name() == 'cli' ) say( self::buildTree( $var ) );
    else                           say( self::dumpForWeb( $var ) );
  }

  /*******************************
   * Web向けにダンプ文字列
   * @param オブジェクトまたは配列
   * @return ダンプ文字列
   *******************************/
  public static function dumpForWeb( $var ){
    $var = self::toArray( $var );
    $ret = '<ul>' . "\n";
    $ret .= '<li><input type="button" onclick="var p=this.parentNode;var c=p.parentNode.getElementsByTagName(\'li\');for(var i=0,l=c.length;i<l;i++)if(c[i]!=p) c[i].style.display=c[i].style.display ? \'\':\'none\';" value="toggle" /></li>';
    foreach( $var as $key => $value ) $ret .= '<li>[' . htmlspecialchars( $key, ENT_QUOTES ). '] =&gt; ' . ( is_scalar( $value ) ?  htmlspecialchars( $value ): self::dumpForWeb( $value ) ) . '</li>' . "\n";
    $ret .=  '</ul>' . "\n";
    return $ret;
  }

  /*******************************
   * 起点からのファイルシステムのツリー構造を取得する
   * @param 起点
   * @return 再帰的な配列
   *******************************/
  public static function getFiles( $path ){
    if( !is_dir( $path ) ) return '';
    $path = preg_replace( '#/$#', '', $path  );
    $d = opendir( $path );
    $ret = array();
    while( $fn = readdir( $d ) ){
      if( preg_match( '#^\.+$#', $fn ) ) continue;
      if ( is_dir ( $path . '/' . $fn ) && preg_match ( '#^.svn#', $fn ) ) continue;
      if    ( is_file( $path . '/' . $fn ) && preg_match( '#php$#', $fn ) ) $ret[]    = preg_replace( '#\.php#', '',  $fn );
      elseif( is_dir( $path . '/' . $fn ) )                                 $ret[$fn] = self::getFiles( $path . '/' . $fn );
    }
    return $ret;
  }

  /*******************************
   * 再帰的名配列をツリーの階層テキストに変換する
   * @param 配列
   * @return 文字列
   *******************************/
  public static function buildTree( $arr, $level = 0 ){
    $ret = '';
    $arr = self::toArray( $arr );
    foreach( $arr as $key => $value ){
      if( is_numeric( $key) ) $key = '- ';
      else                    $key = "$key: ";
      if( is_scalar( $value ) ){
        for( $i = 0; $i < $level; $i++ ) $ret .= ' ';
        if( preg_match( '#\n#', $value ) )  $value = "|\n" . $value;
        else                                $value = '"' . str_replace( '"', '\\"', $value ) . '"';
        $ret .= "$key" . $value  . "\n";
      } else{
        for( $i = 0; $i < $level; $i++ ) $ret .= ' ';
        $ret .= "$key\n";
        $ret .= self::buildTree( $value, $level + 2 );
      }
    }
    return $ret;
  }

  /*******************************
   * fwwObjectを配列化()
   * @param オブジェクト
   * @return 配列
   *******************************/
  public function toArray( $param ){
    foreach( $param as $key => $value ){
      if    ( is_array( $value ) )                                       $param[$key] = self::toArray( $value );
      elseif( is_scalar( $value ) )                                      $param[$key] = $value;
      elseif( is_object( $value ) && get_class( $value ) == 'stdClass' ) $param[$key] = (array)$value;
      else                                                               $param[$key] = 'object';
    }
    return $param;
  }

    /**
     * オブジェクトを依存性代入しながら自動生成する
     * @param $className クラス名
     * @return object 生成したオブジェクト
     * @TODO: シングルトンを考慮する
     */
    public static function createObject($className)
    {
        if (!$className) return null;

        // コンフィグを取得する
        if (preg_match('#^@#', $className)) {
            return aafwApplicationConfig::getInstance()->query($className);
        }

        //  スーパーグローバルを取得する
        elseif (preg_match('#^\$(_[A-Z]+)(\[.+\])?$#', $className, $matches)) {
            $var = null;
            if (isset($matches[2])) {
                $src = '$var = $' . $matches[1] . $matches[2] . ';';
            }
            else {
                $src = '$var = $' . $matches[1] . ';';
            }
            eval($src);
            return $var;
        }

        // 定数を取得する
        elseif (preg_match('#^\!(\S+)$#', $className, $matches)) {
            $var = null;
            $src = '$var = ' . $matches[1] . ';';
            eval($src);
            return $var;
        }

        // Stashを取得する
        elseif (preg_match('#^\%(\S+)$#', $className, $matches)) {
            return self::getStash($matches[1]);
        }

        // シングルトンを取得する
        elseif (preg_match('#^\-(\S+)$#', $className, $matches)) {
            return self::getSingleton($matches[1]);
        }
        else {
            if (!self::classExists($className)) {
                self::import($className);
            }
            $className = self::getClassName($className);
            $class = new \ReflectionClass($className);
            $result = null;
            $method = null;

            // コンストラクタを満たす
            if ($class->hasMethod("__construct")) {
                $method = $class->getMethod('__construct');
                $params = self::createInjector($method);
                $params = self::fillParameterWithNull($method, $params);
                $result = $class->newInstanceArgs($params);
            } else {
                $result = $class->newInstance();
            }
            self::fillDependency($result);
            return $result;
        }
    }


    public static function fillDependency ($obj) {
        $class = new \ReflectionClass(get_class($obj));
        foreach ($class->getMethods() as $method) {
            $params = self::createInjector($method);
            if (!$params) continue;
            $params = self::fillParameterWithNull($method, $params);
            $method->invokeArgs($obj, $params);
        }
    }

    public static function getClassName ($className) {
        $arr = explode('.', $className);
        return array_pop($arr);
    }

    public static function classExists ($className)
    {
        return class_exists(self::getClassName($className));
    }

    public static function getSingleton ($className) {
        if (!self::classExists($className)) {
            self::import($className);
        }
        $class = new \ReflectionClass($className);
        $result = null;
        foreach ($class->getMethods() as $method) {
            if (preg_match( '#^(?:singleton|getInstance)$#', $method->getName()) && $method->isStatic() &&
                !count($method->getParameters())) {
                $result = $method->invoke(null);
                break;
            }
        }
        return $result;
    }
    /**
     * @param $method
     * @throws Exception
     * @internal param $class
     * @return array
     */
    public static function createInjector( $method)
    {
        $params = array();
        $class = $method->getDeclaringClass();
        $docs = $method->getDocComment();
        if ($docs) {
            $params = array();
            $annotation = new aafwAnnotations($docs);
            $inject = $annotation->getAnnotations("inject");
            if (count($inject)) {
                foreach ($inject as $clazz) {
                    if($class->getNameSpaceName() == $clazz[0])
                        throw new Exception('同じクラスのインスタンスの注入は出来ません');
                    $params[] = self::createObject($clazz[0]);
                }
            }
        }
        return $params;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public static function fillParameterWithNull($method, $params)
    {
        $parameterDefinition = $method->getParameters();
        for ($i = count($params); $i < count($parameterDefinition); $i++) {
            if ($parameterDefinition[$i]->isDefaultValueAvailable ()) {
                $params[$i] = $parameterDefinition[$i]->getDefaultValue();
            } elseif ($parameterDefinition[$i]->allowsNull()) {
                $params[$i] = null;
            } elseif (!$parameterDefinition[$i]->isOptional ()) {
                throw new Exception('注入指定の他にNULL許容不可のパラメータが存在します');
            }
        }
        return $params;
    }

    public static function setStash ($key, $val) {
        if (!$key)
            throw new Exception('キーがありません');
        if (preg_match('#\s#', $key))
            throw new Exception('stashのキーに空白を含めることは出来ません');
        if (self::$stash[$key] && self::$stash[$key] != $val)
            throw new Exception('定義済みの値です');
        self::$stash[$key] = $val;
    }

    public static function getStash ($key)  {
        return self::$stash[$key];
    }

    public static function getStashes ()  {
        return self::$stash;
    }

    public static function clearStash () {
        self::$stash = array();
    }
}

//
// コマンドラインから直接呼ばれている場合にはコマンドラインツールを起動
//
if( php_sapi_name() == 'cli' && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_NAME'] ) ) {
  error_reporting ( E_ALL - E_NOTICE );
  AAFW::start();
  $command_name = $argv[3] !== null ? $argv[3] : $argv[2];
  if (!($lock_file = Util::lockFileByName($command_name))) return;
  if ( $argv[1] == 'bat' ||  $argv[1] == 'batch' ) {
//    if (extension_loaded('newrelic')) {
//      $config = aafwApplicationConfig::getInstance();
//        if($config->NewRelic['use']) {
//          newrelic_set_appname($config->NewRelic['consoleApplicationName']);
//      }
//    }
    AAFW::import( 'jp.aainc.aafw.cli.aafwCLIController' );
    array_shift( $argv );
    array_shift( $argv );
    $controller = new aafwCLIController();
    $controller->run( $argv );
  } else {
    AAFW::import( 'jp.aainc.aafw.tools.aafwCommandLineTool' );
    aafwCommandLineTool::doService( $argv );
  }

}

