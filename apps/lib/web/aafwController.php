<?php
AAFW::import ( "jp.aainc.aafw.web.aafwConfig" );
AAFW::import ( "jp.aainc.aafw.base.aafwException" );
/**
 * コントローラコアクラス
 *
 * コントローラの中核処理
 *
 * @package   aafw
 * @author    allied architechts
 */
class aafwController {

  //------------------------------------------------------//
  // クラス変数定義
  //------------------------------------------------------//
  /**
   * クラスインスタンスを保持する変数
   * @access private
   * @var    object
   */
  private static $obj_instance;
  private $REQUEST        = array();
  private $Views          = array();
  private $Actions        = array();
  private $Parsers        = array();
  private $Plugins        = array();
  private $Site           = null;
  private $ActionResult   = null;
  private $ActionSession  = null;
  private $ActionData     = null;
  private $RequestFileType = null;
  private $defaultAction  = 'defaultAction.php';
  private $Result         = '';
  private static $Config  = array();

  /**
   *
   * インスタンスの生成
   *
   * @param     string site
   */
  public static function getInstance($site = null){

    if ( ! isset( self::$obj_instance ) ) {
      $tmpSelf = __CLASS__;
      self::$obj_instance = new $tmpSelf($site);
    }

    return self::$obj_instance;
  }

  public static function clearInstance() {
      self::$obj_instance = null;
  }

  /**
   *
   * コンストラクタ
   *
   * Webの設定値の読み込み
   *
   * @param     string site
   *
   */
  public function __construct($site = null){
    self::$Config = aafwConfig::getApplicationConfig();
    $Sites = explode(',', str_replace(' ', '', self::$Config->Sites));
    if( $site && in_array($site, $Sites) ) $this->Site = $site;
  }

  /**
   * run()
   *
   * コントローラメインロジック
   *
   * @param     void なし
   * @return    void なし
   */
   public function run(){
     if (DEBUG) {
       aafwLog4phpLogger::getDefaultLogger()->debug("start: " . $_SERVER['REQUEST_URI']);
     }
     $Config = self::$Config;
     $Parsers = self::$Config->Parsers;

     $sessionClass = 'aafw' . $Config->SessionType . 'Session';
     AAFW::import('jp.aainc.aafw.session.'. $sessionClass );
     $sessionObj = new $sessionClass($Config);
     if ( $Config->Headers ) {
         foreach ( $Config->Headers as $header ) {
             header ( $header );
         }
     }

     $this->REQUEST['POST']    = $_POST;
     $this->REQUEST['GET']     = $_GET;
     $this->REQUEST['COOKIE']  = $_COOKIE;
     $this->REQUEST['FILES']   = $_FILES;
     $this->REQUEST['REQUEST'] = $_REQUEST;
     $this->REQUEST['ENV']     = $_ENV;
     $this->REQUEST['SERVER']  = $_SERVER;
     $this->REQUEST['SESSION'] = $sessionObj->getValues();

	   /// プラグインのロード
	   if( is_dir( $Config->PluginDir = preg_replace( '#/$#', '', AAFW_DIR ) . '/plugins/controller'  )  ){
		   $plugins = opendir( $Config->PluginDir );
		   while( $plugin = readdir( $plugins ) ){
			   if( !preg_match( '#\.php$#', $plugin ) ) continue;
			   if( is_file(  $Config->PluginDir . '/' . $plugin ) ){
				   require_once $Config->PluginDir . '/' . $plugin;
				   $class = str_replace( '.php', '', $plugin );
				   $c =  new $class( $this );
				   $Plugins[$c->getHookPoint()][] = $c;
			   }
		   }
		   if( count( $Plugins ) ){
			   foreach ( $Plugins  as $key => $value ) {
				   usort ( $value, create_function ( '$x, $y', 'return $x->getPriority() == $y->getPriority() ? 0 : ($x->getPriority() < $y->getPriority() ? -1 : 1);' ) );
				   $Plugins[$key] = $value;
			   }
		   }
	   }

     ///
     /// プラグイン( First )
     ///
     if( count( $Plugins['First'] ) ){
       foreach( $Plugins['First'] as $c ) $ret = $c->doService();
     }

     $Config = self::$Config;

     $p  = $this->REQUEST['POST'];
     $g  = $this->REQUEST['GET'];
     $s  = $this->REQUEST['SESSION'];
     $c  = $this->REQUEST['COOKIE'];
     $f  = $this->REQUEST['FILES'];
     $r  = $this->REQUEST['REQUEST'];
     $e  = $this->REQUEST['ENV'];
     $sv = $this->REQUEST['SERVER'];
     $req          = '';
     $action = array( 'name' => '', 'package' => '' );
     if( @$g['action']  ) $action['name']    = $g['action'];
     if( @$g['package'] ) $action['package'] = $g['package'];
     if( @$g['req'] )     $req = strtoupper( $g['req'] );
     if( !$req || !$Parsers[$req] ) $req ='PHP';

     if( $this->Site ) $sitePath = $this->Site . '/';
     if( $action['package'] ){
       $action['path'] = $Config->ActionPath . '/' . $sitePath . $action['package'] . '/' . $action['name'] . '.php';
     } else {
       $action['path'] =  $Config->ActionPath . '/' . $sitePath . $action['name'] . '.php';
     }

     if( !is_file( $action['path'] ) ){
       $action['path'] = $Config->ActionPath . '/' . $sitePath . $Config->defaultAction . '.php';
       $action['name'] = $Config->defaultAction;
     }
     $result = '';
     try {
       //
       // reqやらactionやら、その他パラメータのエンコード・ファイルのアップロード等
       //
       if( !$action['name'] )
         throw new Exception( 'actionの指定がありません' );

       if( !is_file( $action['path'] ) ) {
           return $this->notFound( array() );
       }

	   $tmpdir = '';
	   $save_dir_list = array();
	   if( count( $f ) ){
	     list( $tmpdir, $files ) = array( $Config->TmpDir . '/' . uniqid() , array() );
		 mkdir( $tmpdir );
		 foreach( $f as $key => $value ) {
		   if( !$value['name'] ) continue;
		     $save_dir = $tmpdir . '/' . md5(mt_rand() + time());
		     mkdir( $save_dir );
			 $save_dir_list[]= $save_dir;

		     $files[$key] = array();
             if (is_array($value['name'])) {
               for ($i = 0; $i < count($value['name']) ; $i++) {
                 $files[$key]['name'][$i] = $save_dir . '/' . preg_replace('#[/ 　]#u', '', $value['name'][$i]);
                 if ($value['error'][$i]) {
                   $files[$key]['error'][$i] = $value['error'][$i];
                 } else {
                   !move_uploaded_file($value['tmp_name'][$i], $files[$key]['name'][$i]);
                   if (!($files[$key]['type'][$i] = $this->getFileType($files[$key]['name'][$i])) &&
                       (preg_match('#/(.+)$#', $value['type'][$i], $t) ||
                           preg_match('#\.([^\.]+)$#', $value['name'][$i], $t))
                   ) {
                     $files[$key]['type'][$i] = $t[1];
                   }
                 }
               }
             } else {
               $files[$key]['name'] = $save_dir . '/' . preg_replace('#[/ 　]#u', '', $value['name']);
               if ($value['error']) {
                 $files[$key]['error'] = $value['error'];
               } else {
                 !move_uploaded_file($value['tmp_name'], $files[$key]['name']);
                 if (!($files[$key]['type'] = $this->getFileType($files[$key]['name'])) &&
                     (preg_match('#/(.+)$#', $value['type'], $t) ||
                         preg_match('#\.([^\.]+)$#', $value['name'], $t))
                 ) {
                   $files[$key]['type'] = $t[1];
                 }
               }
             }
		 }
	     $f = $files;
	   }

       //
       // アクションの実行
     //
       require_once( $action['path'] );
       $settings = array();
       foreach( $Config->getValues() as $key => $value ) $settings[$key] = $value;

        if (extension_loaded('newrelic')) {
         $config = aafwApplicationConfig::getInstance();
         if($config->NewRelic['use']) {
             newrelic_set_appname($config->NewRelic['consoleApplicationName']);
             newrelic_name_transaction($action['package'] . '/' . $action['name']);
         }
        }

       $action = new $action['name']( $p, $g, $s, $c, $f, $e, $sv, $r,
         $this->getSite() ,
         $action['package'],
         $settings );
       $this->ActionResult  = $action->run();
       $this->ActionData    = $action->getData();
       $this->ActionData['__ACTION__'] = $action;
       $this->ActionSession = $action->getSession();
       ///
       /// プラグイン( AfterService )
       ///
       if( @count( $Plugins['AfterService'] ) ){
         foreach( $Plugins['AfterService'] as $c ) $ret = $c->doService();
       }
       $Config = self::$Config;

       $result  = $this->ActionResult;
       $data    = $this->ActionData;
       $ses     = $this->ActionSession;

       if (DEBUG) {
         aafwLog4phpLogger::getDefaultLogger()->debug(
             "action result: result=" . $result . ", data=" . json_encode($data, JSON_PRETTY_PRINT) . ", ses=" . json_encode($ses, JSON_PRETTY_PRINT));
       }

       // セッションが有る場合は書き戻す
       if (is_array($ses)) {

         $sessionObj->setValues( $ses );
       } elseif( $ses == 'clear' || !$ses ){
         $sessionObj->clear();
       }

       // ファイルアップロード用の一時ディレクトリは削除しとく
       if( is_dir( $tmpdir ) ){
         foreach( glob( $tmpdir . '/*' ) as $f )  unlink( $f );
         rmdir( $tmpdir );
       }

       // リダイレクト指定がある場合にはリダイレクトしちゃる
       if ( $req == 'PHP' && preg_match( '/^redirect *: *(\S+)/', $result, $tmp ) ) {
         if( !$tmp[1] ) throw new Exception( 'リダイレクト先の指定が有りません。' );
         return $this->redirect( $tmp[1] );
       }

       elseif ( $req == 'PHP' && preg_match( '/^redirect301 *: *(\S+)/', $result, $tmp ) ) {
         if( !$tmp[1] ) throw new Exception( 'リダイレクト先の指定が有りません。' );
         return $this->redirectPermanent( $tmp[1] );
       }

       elseif( preg_match( '#(404|not found)#i', $result, $tmp ) ) {
         $this->RequestFileType = 'PHP';
         $this->Result          = $this->notFound( $data );
         if ( @count( $Plugins['Last'] ) ){
           foreach( $Plugins['Last'] as $c ) $ret = $c->doService();
         }
         return $this->Result;
       }
       elseif( preg_match( '#(403|forbidden)#i', $result, $tmp ) ) {
         $this->RequestFileType = 'PHP';
         $this->Result          = $this->forbidden( $data );
         if( @count( $Plugins['Last'] ) ){
           foreach( $Plugins['Last'] as $c ) $ret = $c->doService();
         }
         return $this->Result;
       }
       else {
         if     ( preg_match ( '#^bin:\s*(.+)$#i', $result, $tmp ) ) $req = strtoupper( $tmp[1] );
         elseif ( !in_array( $req, $action->getAllowContent() ) )    throw new Exception( '許可されないリクエストです。' );
         // PHP書き出しで、テンプレートの指定がない場合は例外投げる
         if( $req == 'PHP' || $req == 'JS' ){
           if( !$result || !is_file( $Config->TemplatePath . DIRECTORY_SEPARATOR .  $result ) ) {
             throw new Exception('テンプレートの指定が無いか間違っています : ' . $result );
           } else {
             if( !is_array( $data ) ) throw new Exception( 'データが配列ではありません' );
             $data['__view__'] = $Config->TemplatePath . DIRECTORY_SEPARATOR .  $result;
             $data['__REQ__']  = $action->getRequest();
           }
         }

         $this->RequestFileType = $req;

         //
         // パース
         //
         require_once $Config->Parsers[$req]['path'];
         $parser = new $Config->Parsers[$req]['classname'];
         $result = $this->Result = $parser->out( $data );
         header( 'Content-type: ' . $parser->getContentType() );
         if( in_array( 'getDisposition' ,get_class_methods( $parser ) ) ){
           foreach( preg_split( "#\n+#", $parser->getDisposition() ) as $row ){
             if( preg_match( '#^\s*$#', $row ) ) continue;
             header( $row );
           }
         }
         //
         // デバッグモードの書き出し
         //
         if( $Config->Debug && $req == 'PHP' ) {
           ob_start();
           print "--------- デバッグ情報 ---------\n";
           print "<<<<<< REQUEST >>>>>>\n";
           var_dump( $this->REQUEST );
           print "\n\n";
           print "<<<<<< data >>>>>>\n";
           var_dump( $data );
           print "\n\n";
           $dump = ob_get_clean();

           $this->Result .= "<pre>\n";
           $this->Result .= htmlspecialchars ( $dump, ENT_QUOTES );
           $this->Result .= '</pre>';
         }
       }

       ///
       /// プラグイン( Last )
       ///
       if( @count( $Plugins['Last'] ) ){
         foreach( $Plugins['Last'] as $c ) $ret = $c->doService();
       }
       $Config = self::$Config;
       $result = $this->Result;

       if (DEBUG) {
         aafwLog4phpLogger::getDefaultLogger()->debug("loaded schemas: " . join(",", array_keys(aafwEntityStoreBase::getCatalogs())));
       }
     } catch( Exception $e ) {

       // ログを書き出す
       $this->loggingError($e);

       $this->RequestFileType = $req;
       if( $req == 'PHP') $result = $this->renderErrorPage( $e );
       else               $result = 'NG:' . $e->getCode();
       $this->Result = $result ;
       ///
       /// プラグイン( AfterService )
       ///can not get property
       if( @count( $Plugins['Last'] ) ) {
         foreach( $Plugins['Last'] as $c ) $ret = $c->doService();
       }
       $Config = self::$Config;
       $result = $this->Result;
     }
     if ( is_resource ( $result ) ) {
       while ( $buf = fread ( $result, 1024 ) ) print $buf;
       return '';
     }
     else {
       return $result;
     }
   }

  /***********************************
   * エラーログを出力する
  ***********************************/
  private function loggingError($e) {
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error($e);
  }

  /***********************************
   * Actionディレクトリのパスを返す
   ***********************************/
  public function getActionPath(){
    return self::$Config->ActionPath;
  }

  /***********************************
   * 実行しているサブディレクトリを返す
   ***********************************/
  public function getSubDirectory(){
    return self::$Config->SubDirectory;
  }

  /***********************************
   * パラメータを返す
   ***********************************/
  public function getRequest(){
    return $this->REQUEST;
  }

  /***********************************
   * パラメータを設定する
   ***********************************/
  public function setRequest( $r ){
    $this->REQUEST = $r;
  }

  /***********************************
   * 実行したアクションの結果を返す
   ***********************************/
  public function getActionResult(){
    return $this->ActionResult;
  }

  /***********************************
   * 実行したアクションの結果を捏造して上書きする
   ***********************************/
  public function setActionResult( $value ){
    $this->ActionResult = $value;
  }

  /***********************************
   * 実行したアクションのデータを返す
   ***********************************/
  public function getActionData(){
    return $this->ActionData;
  }

  /***********************************************
   * 実行したアクションのデータを捏造して上書きする
   **********************************************/
  public function setActionData( $value ){
    $this->ActionData = $value;
  }

  /***********************************************
   * 実行したアクションのセッションを返す
   **********************************************/
  public function getActionSession(){
    return $this->ActionSession;
  }

  /***********************************************
   * 実行したアクションのセッションを捏造して上書きする
   **********************************************/
  public function setActionSession( $value ){
    $this->ActionSession = $value;
  }

  public function getRequestFileType () {
    return  $this->RequestFileType;
  }

  /**
   *
   */
  public function getControllerResult(){
    return $this->Result;
  }

  /**
   *
   */
  public function setControllerResult( $value ){
    $this->Result = $value;
  }

  /**
   *
   */
  public function getConfig(){
    return self::$Config;
  }

  /**
   *
   */
  public function setConfig( $value ){
    self::$Config = $value;
  }

  /***********************************
   * siteを返す
   ***********************************/
  public function getSite(){
    return $this->Site;
  }

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
      $this->REQUEST['POST'],
      $this->REQUEST['GET'],
      $this->REQUEST['SESSION'],
      $this->REQUEST['COOKIE'],
      $this->REQUEST['FILES'],
      $this->REQUEST['ENV'],
      $this->REQUEST['SERVER'],
      $this->REQUEST['REQUEST']
      ) = array( $p, $g, $s, $c, $f, $e, $sv, $r );
  }

  /*************************
   * 片っ端から全部取得
   *************************/
  public function getParams(  ){
    return array(
      $this->REQUEST['POST'],
      $this->REQUEST['GET'],
      $this->REQUEST['SESSION'],
      $this->REQUEST['COOKIE'],
      $this->REQUEST['FILES'],
      $this->REQUEST['ENV'],
      $this->REQUEST['SERVER'],
      $this->REQUEST['REQUEST'],
      );
  }

  /************************************
   * エラー表示
   * @param - Exception
   **********************************/
  private function renderErrorPage( $e ){
    $Config        = self::$Config;
    $Parsers       = self::$Config->Parsers;
    $path          = $Config->TemplatePath . DIRECTORY_SEPARATOR .  $Config->ErrorPage;

    $data['code'] = $e->getCode() . "\n";
    $data['message']   = $e->getMessage() . "\n";
	ob_start();
    if( is_file( $path ) ){
      require_once $Parsers['PHP']['path'];
      $c = new $Parsers['PHP']['classname'];
      if ( !$data['__view__'] )           $data['__view__'] = $path;
      $data['__REQ__'] = array( 'exception' => $e );
      print $c->out( $data );
    } else {
      print '致命的なエラーです';
      if ( self::$Config->Debug ) {
        print '<pre>';
        var_dump( $e );
        print '</pre>';
      }
    }
    if( self::$Config->Debug  ) {
      print "<pre>";
      print "メッセージ\n";
      var_dump ( $e instanceof aafwException ? $e->getAppErrorCode () : $e->getMessage () );
      print "\n\n";
      print "スタックトレース\n";
      var_dump( $e->getTrace() );
      print "\n\n";
      print "Controlerの中身\n";
      var_dump( $this );
      print "</pre>";
    }
    return ob_get_clean();
  }

  /************************************
   * redirect
   * @param - url
   **********************************/
  private function redirect( $url ){
    header( 'Location: '. $url );
  }

  private function redirectPermanent ( $url ) {
    header ( "HTTP/1.1 301 Moved Permanently" );
    header ( 'Location: '. $url );
  }

  /************************************
   * notfound
   * @param - url
   **********************************/
  private function notFound( $data ){
    header("HTTP/1.1 404 Not Found");

    $Config = self::$Config;
    $Parsers = self::$Config->Parsers;

    $path =  $Config->TemplatePath . DIRECTORY_SEPARATOR .  $Config->NotFound;
    require_once $Parsers['PHP']['path'];
    $c = new $Parsers['PHP']['classname'];
    if( !$data['__view__'] ) $data['__view__'] = $path;
    return $c->out( $data );
  }

  /************************************
   * forbidden
   * @param - url
   **********************************/
  private function forbidden( $data ){
    header("HTTP/1.1 403 Forbidden");
    $Config = self::$Config;
    $Parsers = self::$Config->Parsers;
    require_once $Parsers['PHP']['path'];
    $c = new $Parsers['PHP']['classname'];
    $path =  $Config->TemplatePath . DIRECTORY_SEPARATOR .  $Config->Forbidden;
    if( !$data['__view__'] ) $data['__view__'] = $path;
    return $c->out( $data );
  }

  /**
   * ファイルタイプの判別を行う(JPG|PNG|GIF)、
   * それ以外ならとりあえずfalseと言う手抜きっぷり
   * @param - path
   * @return png|gif|JPG|false
   */
  public function getFileType( $path ){
    $f = fopen( $path, 'r' );
    $data = fread( $f, 8 );
    fclose( $f );
    if    ( preg_match( '#^\x89PNG#' , $data ) ) return 'png';
    elseif( preg_match( '#^GIF#'     , $data ) ) return 'gif';
    elseif( preg_match( '#^\xFF\xD8#', $data ) ) return 'JPG';
    else                                         return false;
  }
}
