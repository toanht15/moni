<?php
/*********************************
 * MVCのCのCの中身
 * @author t.ishida
 * @cre    2008/02/24
 **********************************/
AAFW::import ( 'jp.aainc.aafw.base.aafwValidatorBase' );
AAFW::import ( 'jp.aainc.aafw.aafwValidator' );
AAFW::import ( 'jp.aainc.aafw.aafwApplicationConfig' );

abstract class aafwActionBase extends aafwValidatorBase {
  protected $POST    = null;
  protected $GET     = null;
  protected $SESSION = null;
  protected $COOKIE  = null;
  protected $FILES   = null;
  protected $SERVER  = null;
  protected $REQUEST = null;
  protected $ENV     = null;

  protected $ErrorPage    = 'error_page.php';
  protected $Settings     = '';
  protected $AppConfig    = '';
  protected $WebSettings  = array();
  protected $ContainerName   = '';

  protected $_Plugins = array(
    'First'         => array(),
    'BeforeService' => array(),
    'Last'          => array(),
    );

  protected $PackageName   = '';
  protected $Data          = array();
  /** @var aafwValidator string $Validator*/
  protected $Validator     = '';
  protected $AllowContent  = array(  'HTML', 'PHP' );
  protected $_Site          = '';
  protected $_ModelDefinitions = array ();
  protected $_Models           = array ();
  protected $_ServiceFactory   = null;

  /*************************
   * コンストラクタ
   *************************/
  public function __construct(
    $p = array(),
    $g = array(),
    $s = array(),
    $c = array(),
    $f = array(),
    $e = array(),
    $sv = array(),
    $r  = array() ,
    $site = '',
    $pkg = '',
    $web_settings = array()
    ){
    if ( $pkg )          $this->PackageName = $pkg;
    if ( $site )         $this->_Site       = $site;
    if ( $web_settings ) $this->WebSettings = $web_settings;
    $this->rewriteParams( $p, $g, $s, $c, $f, $e, $sv, $r );
    $this->AppConfig = aafwApplicationConfig::getInstance();
    $this->Settings  = aafwApplicationConfig::getInstance()->getValues();
    $this->loadPlugins ();
	$this->sortPlugins();
    $this->loadValidator ();
  }

  // ActionContainerセッション管理

  public function getContainerType() {
    return 'common';
  }

  /**
   * @return bool
   */
  public function issetContainerName() {
    return $this->ContainerName ? true : false;
  }

  /**
   * @param $params
   */
  public function setActionContainers($params) {
    if (!$this->issetContainerName()) return;

    foreach ($params as $key => $value) {
      $this->SESSION['ActionContainer'][$this->getContainerType()][$this->ContainerName][$key] = $value;
    }
  }

  /**
   * @param $key
   * @return mixed
   */
  public function getActionContainer($key) {
    if (!$this->issetContainerName()) return;

    return $this->SESSION['ActionContainer'][$this->getContainerType()][$this->ContainerName][$key];
  }

  /**
   * @param $key
   */
  public function resetActionContainerByKey($key) {
    if (!$this->issetContainerName()) return;

    unset($this->SESSION['ActionContainer'][$this->getContainerType()][$this->ContainerName][$key]);
  }

  public function resetActionContainerByName() {
    unset($this->SESSION['ActionContainer'][$this->getContainerType()][$this->ContainerName]);
  }

  public function resetActionContainerByType() {
    unset($this->SESSION['ActionContainer'][$this->getContainerType()]);
  }

  public function resetValidateError() {
    if (!$this->getActionContainer('Errors')) {
      $this->resetActionContainerByKey('ValidateError');
    }
  }

    public function resetResult() {
        $this->resetActionContainerByKey('Result');
    }

  /**
   * プラグインを空にして実行にする
   */
  public function disablePlugins () {
    $this->_Plugins = array (
      'First'         => array(),
      'BeforeService' => array(),
      'Last'          => array(),
    );
  }

  /**
   * モデルの設定を返す
   * @return モデルの設定
   */
  public function getModelDefinitions () {
    return $this->_ModelDefinitions;
  }

  /*****************************
   * デフォルトのセッタ
   *****************************/
  public function __set( $key, $value ){
    $this->REQUEST[$key] = $value;
  }

  /*****************************
   * デフォルトのゲッタ
   *****************************/
  public function __get( $key ){
    return $this->REQUEST[$key];
  }

  /*****************************
   * 許可するContent-Typeを返す
   *****************************/
  public function getAllowContent() {
    return is_array( $this->AllowContent ) ? $this->AllowContent : array( $this->AllowContent );
  }

  /*************************
   * データを返す
   *************************/
  public function getData(){
    return $this->Data;
  }

  /*************************
   * データを返す
   *************************/
  public function setData( $value ){
    $this->Data = $value;
  }

  /*************************
   * リクエストを返す
   *************************/
  public function getRequest ( $key = null ) {
    if ( !$key ) return $this->REQUEST;
    else         return $this->REQUEST[$key];
  }

  /*************************
   * 片っ端から全部取得
   *************************/
  public function getParams(  ){
    return array(
      $this->POST,
      $this->GET,
      $this->SESSION,
      $this->COOKIE,
      $this->FILES,
      $this->ENV,
      $this->SERVER,
      $this->REQUEST
      );
  }

  /**
   * 特別な理由がない限りは、代わりにgetBrandSession、もしくはgetSharedCriticalResourceSession関数経由で取得してください。
   * @deprecated
   */
  public function getSession ( $key = null ) {
    if ( !$key ) return $this->SESSION;
    else         return $this->SESSION[$key];
  }

  /**
   * ブランドごとのセッション情報を取得します。
   */
  public function getBrandSession($key = null) {
    $brand = BrandInfoContainer::getInstance()->getBrand();
    if ($brand === null) {
      return null;
    }
    return $this->getSession($key . $brand->id);
  }

  /**
   * 非常に注意して利用すべき、共有のセッションを取得します。
   */
  public function getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully($key = null) {
    return $this->getSession($key);
  }

  /**
   * 特別な理由がない限りは、代わりにsetBrandSession、もしくはsetSharedCriticalResourceSession関数経由で設定してください。
   * @deprecated
   */
  public function setSession ( $key, $val ) {
    if ($val === null) {
      // セッションにゴミが残り、キャッシュを圧迫するのを防ぐため、
      // データを削除しておきます。
      unset($this->SESSION[$key]);
    } else {
      $this->SESSION[$key] = $val;
    }
  }

  /**
   * ブランドごとのセッション情報を取得します。
   */
  public function setBrandSession($key = null, $val) {
    $brand = BrandInfoContainer::getInstance()->getBrand();
    if ($brand === null) {
      return null;
    }
    $this->setSession($key . $brand->id, $val);
  }

  /**
   * 非常に注意して利用すべき、共有のセッションを設定します。
   */
  public function setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully($key = null, $val) {
    $this->setSession($key, $val);
  }


  public function getSettings(){
    return $this->Settings;
  }

  public function setAppConfig ( $obj ) {
    $this->AppConfig = $obj;
  }


  ///
  /// バリデータのロード
  ///
  public function loadValidator () {
    if( $this->Validator && is_dir( $dir = preg_replace( '#/$#', '', AAFW_DIR ) . '/plugins/validator' ) ) {
      $class = $this->Validator;
      if( !is_dir( $dir ) ) throw new Exception( 'バリデータのディレクトリが存在しません:' . $dir );
      require_once $dir . '/' . $class . '.php';
      $v = new $class();
      $this->Validator = $v;
    }
  }

  ///
  /// プラグインのロード
  ///
  public function loadPlugins ( $pluginDir = '' ) {
	  if( $pluginDir == '' ) {
		  $pluginDir = preg_replace( '#/$#', '', AAFW_DIR ) . '/plugins/action';
	  }
	  if ( is_dir( $pluginDir ) ) {
		  $plugins = opendir( $pluginDir );
		  while( $plugin = readdir( $plugins ) ){
			  if ($plugin == '.' || $plugin == '..') continue;
			  if( is_file(  $pluginDir . '/' . $plugin ) ){
				  if( !preg_match( '#\.php$#', $plugin ) ) continue;
				  require_once $pluginDir . '/' . $plugin;
				  $class = str_replace( '.php', '', $plugin );
				  $c =  new $class( $this );
				  if ( !$c->canRun () ) continue;
				  $this->_Plugins[$c->getHookPoint()][] = $c;
			  } elseif ( is_dir( $pluginDir . '/' . $plugin ) && $plugin == $this->getSite() ) {
				  foreach( $Plugins = $this->loadPlugins( $pluginDir . '/' . $plugin ) as $key => $value ) {
					  $this->_Plugins[$key] = $value;
				  }
			  }
		  }
		  return $Plugins = $this->_Plugins;
	  }
  }
	public function sortPlugins () {
		foreach ( $this->_Plugins  as $key => $value ) {
			usort ( $value, create_function ( '$x, $y', 'return $x->getPriority() == $y->getPriority() ? 0 : ($x->getPriority() < $y->getPriority() ? -1 : 1);' ) );
			$this->_Plugins[$key] = $value;
		}
	}

  public function getSite () {
    return $this->_Site;
  }

  /*************************
   * サービスを記述します。
   * {
   *    //セッションに何か保存したければ
   *    $this->SESSION['foo'] = 'bar';
   *    $this->Data = array( [ビューに渡すデータ構造] );
   *    return 'ビューの名前';  //ビューが必要無ければ書かなくても おけ
   * }
   *************************/
  abstract function doService( );

  abstract function validate ();


  /*************************
   * パラメータの上書き
   *************************/
  public function rewriteParams(
    $p = array(),
    $g = array(),
    $s = array(),
    $c = array(),
    $f = array(),
    $e = array(),
    $sv = array(),
    $r  = array() ){

    list(
      $this->POST,
      $this->GET,
      $this->SESSION,
      $this->COOKIE,
      $this->FILES,
      $this->ENV,
      $this->SERVER,
      $this->REQUEST
      ) = array( $p, $g, $s, $c, $f, $e, $sv, $r );
  }

  /***********************************
   * Actionの実施
   ***********************************/
  public function run( ){

    $methods = get_class_methods( $this );

      ///
      /// プラグイン( Zero )
      ///
      if (count($this->_Plugins['Plugin/Zero'])) {
          foreach ($this->_Plugins['Plugin/Zero'] as $c) {
              $ret = $c->doService();
              if (preg_match('#^redirect|404|403#i', $ret)) {
                  if (count($this->_Plugins['Finally']))
                      foreach ($this->_Plugins['Finally'] as $c) $c->doService();

                  if (DEBUG) {
                    aafwLog4phpLogger::getDefaultLogger()->debug(
                        "returning by plugin(zero) : class=" . get_class($c) . ", result=" . $ret);
                  }

                  return $ret;
              }
          }
      }

    ///
    /// まずはじめに呼ばれるメソッド
    ///
    if( in_array( 'doThisFirst', $methods ) ){
      $ret = $this->doThisFirst();
      if( $this->canStop ( $ret ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by doThisFirst :  result=" . $ret);
        }
        return $ret;
      }
    }

    ///
    /// プラグイン( First )
    ///
    if( count( $this->_Plugins['First'] ) ){
      foreach( $this->_Plugins['First'] as $c ){
        $ret = $c->doService();
        if( $this->canStop( $ret ) ) {
          $this->doPlugin ( 'Finally' );
          if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug(
                "stopping by plugin(First) : class=" . get_class($c) . ", result=" . $ret);
          }
          return $ret;
        }
      }
    }

    ///
    /// validation前に呼ばれるメソッド
    ///
    if( in_array( 'beforeValidate', $methods ) ){
      $ret = $this->beforeValidate();
      if( $this->canStop ( $ret ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by beforeValidate");
        }
        return $ret;
      }
    }

    ///
    /// バリデーション
    ///
    if( $this->Validator ){
      $this->Validator->setParams(
        $this->POST,
        $this->GET,
        $this->SESSION,
        $this->COOKIE,
        $this->FILES,
        $this->ENV,
        $this->SERVER,
        $this->REQUEST ,
        $this->Settings
      );
      $ret = $this->Validator->validate();
      if ( $this->canStop ( $ret ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by validator : result=" . $ret);
        }
        return  $ret;
      }
      if ( !$ret ) {
        $this->Data = $this->Validator->getData();
        $this->doPlugin ( 'Finally' );
        return $this->ErrorPage;
      }
    } else {
      if ( in_array( 'validate', $methods ) ) {
        $ret = $this->validate();
        if ( $this->canStop ( $ret ) ) {
          $this->doPlugin ( 'Finally' );
          if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug(
                "stopping by validate method : result=" . $ret);
          }
          return $ret;
        }
        if ( !$ret ) {
          $this->doPlugin ( 'Finally' );
          return $this->ErrorPage;
        }
      }
      elseif ( $this->ValidatorDefinition ) {
        $validator = new aafwValidator( $this->ValidatorDefinition );
        if ( !$validator->validate ( $this->REQUEST ) ){
          $this->Data['validator'] = $validator;
          $this->doPlugin ( 'Finally' );
          return $this->ErrorPage;
        }
      } else {
        throw new Exception( get_class( $this ) . 'にvalidateメソッドを実装して下さい。' );
      }
    }

    ///
    /// validation後に呼ばれるメソッド
    ///
    if ( in_array( 'afterValidate'  , $methods ) ) {
      $ret = $this->afterValidate();
      if ( $this->canStop ( $ret  ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by afterValidate : result=" . $ret);
        }
        return $ret;
      }
    }

    ///
    /// プラグイン( BeforeService )
    ///
    if ( count( $this->_Plugins['BeforeService'] ) ){
      foreach( $this->_Plugins['BeforeService'] as $c ){
        $ret = $c->doService();
        if ( $this->canStop ( $ret  ) ) {
          $this->doPlugin ( 'Finally' );
          if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug(
                "stopping by BeforeService : class=" . get_class($c) . ", result=" . $ret);
          }
          return $ret;
        }
      }
    }

    ///
    /// 主処理前に呼ばれるメソッド
    ///
    if ( in_array( 'beforeDoService', $methods ) ) {
      $ret = $this->beforeDoService();
      if ( $this->canStop ( $ret  ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by beforeDoService : class=" . get_class($c) . ", result=" . $ret);
        }
        return $ret;
      }
    }

    ///
    /// 主処理
    ///
    $action_ret = $this->doService();

    ///
    /// 主処理後に呼ばれるメソッド
    ///
    if( in_array( 'afterDoService', $methods ) ) {
      $ret = $this->afterDoService();
      if ( $this->canStop ( $ret  ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by afterDoService : result=" . $ret);
        }
        return $ret;
      }
    }

    ///
    /// プラグイン( Last )
    ///
    if( count( $this->_Plugins['Last'] ) ){
      foreach( $this->_Plugins['Last'] as $c ){
        $ret = $c->doService();
        if ( $this->canStop ( $ret  ) ) {
          $this->doPlugin ( 'Finally' );
          if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug(
                "stopping by Last : class=" . get_class($c) . ", result=" . $ret);
          }
          return $ret;
        }
      }
    }

    ///
    /// プラグイン( Finally )
    ///
    if( count( $this->_Plugins['Finally'] ) ){
      foreach( $this->_Plugins['Finally'] as $c ){
        $ret = $c->doService();
        if ( $this->canStop ( $ret  ) ) {
          $this->doPlugin ( 'Finally' );
          if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug(
                "stopping by Finally : class=" . get_class($c) . ", result=" . $ret);
          }
          return $ret;
        }
      }
    }

    ///
    /// 本当に最後に呼ばれるメソッド
    ///
    if( in_array( 'doThisLast', $methods ) ) {
      $ret = $this->doThisLast();
      if ( $this->canStop ( $ret  ) ) {
        $this->doPlugin ( 'Finally' );
        if (DEBUG) {
          aafwLog4phpLogger::getDefaultLogger()->debug(
              "stopping by doThisLast : class=" . get_class($c) . ", result=" . $ret);
        }
        return $ret;
      }
    }

    if (DEBUG) {
      aafwLog4phpLogger::getDefaultLogger()->debug("returning action result: " . $action_ret);
    }

    return $action_ret;
  }

  public function assign (  ) {
    if ( !func_num_args() ) throw new aafwException ( '引数がありません' );
    if     ( func_num_args() == 1 ) $this->Data = func_get_arg(0);
    elseif ( func_num_args() == 2 ) $this->Data[func_get_arg(0)] = func_get_arg(1);
    else                            throw new aafwException ( '引数の数が不正です' );
  }

  public function refference ( $key ) {
    return $this->Data[$key];
  }

  public function setServiceFactory ( $obj ) {
    $this->_ServiceFactory = $obj;
  }

    public function createService ( $name, $params = null ) {
        if ( !$this->_ServiceFactory )
            $this->_ServiceFactory = new aafwServiceFactory();
        return $this->_ServiceFactory->create ( $name, $params );
    }

  public function getSessionID ( ) {
    return session_id() . '';
  }

  public function setServer ( $key, $val ) {
    $this->SERVER[$key] = $val;
  }

  public function getServer ( $key ) {
    return $this->SERVER[$key];
  }

  public function doPlugin ( $phase ) {
    if( count( $this->_Plugins[$phase] ) ) {
      foreach( $this->_Plugins[$phase] as $c ) {
        $c->doService();
      }
    }
  }

  public function canStop ( $ret )  {
    return preg_match ( '#(?:redirect|404|not found|403|forbidden)#', $ret );
  }

  public function createAjaxResponse($result, $data=array(), $errors=array(), $html="") {
 	$json_data = array();
	$json_data["result"] = $result;
	$json_data["data"] = $data;
	$json_data["errors"] = $errors;
	$json_data["html"] = $html;
	return $json_data;
  }

  public function createApiResponse($status, $data=array(), $error) {
    $json_data = array();
    $json_data["status"] = $status;
    $json_data["data"] = $data;
    $json_data["error"] = $error;
    return $json_data;
  }

}
