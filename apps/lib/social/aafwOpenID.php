<?php
require_once 'Auth/OpenID/Consumer.php';
require_once 'Auth/OpenID/FileStore.php';
require_once 'Auth/OpenID/SReg.php';
class aafwOpenID {
  /**
   * @var Auth_OpenID_Consumer
   */
  protected $_consumer = NULL;

  /**
   * OpenID関連で生成されるファイル格納ディレクトリパス
   * @var string
   */
  public $_store_path = "/data/tmp";

  /**
   * 承認フラグ
   * @var bool
   */
  protected $_valid = false;

  /**
   * 認証レスポンス
   * @var object
   */
  protected $_response = NULL;

  /**
   * 取得内容
   * @var array
   */
  protected $_contents = array();

  /**
   * コンストラクタ
   */
  public function __construct() {
    // Windowsの場合カーネル乱数ジェネレータを使用しない
    if (!strncmp(PHP_OS, 'WIN', 3)) {
      define('Auth_OpenID_RAND_SOURCE', NULL);
    }

    if (!file_exists($this->_store_path) && !mkdir($this->_store_path)) {
      die("Could not create the FileStore directory");
    }
    $this->_consumer = new Auth_OpenID_Consumer(new Auth_OpenID_FileStore($this->_store_path));
  }
  
  /**
   * OpenIDによる認証
   *
   * @param string $checkid
   * @param string $process_path
   * @return bool 認証失敗時
   */
  public function authenticate($checkid, $process_path) {
    // 接続スキーマ
    $scheme = 'http';
    $scheme = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

    // 承認URL
    $trust_root = sprintf(
      "$scheme://%s:%s%s",
      $_SERVER['SERVER_NAME'],
      $_SERVER['SERVER_PORT'],
      dirname( $_SERVER['REQUEST_URI'] )
      );

    // 承認後リダイレクト先URL
    $process_url = $trust_root . $process_path;

    // 認証開始
    $auth_request = $this->_consumer->begin($checkid);

    // 入力されたOpenIDが使用出来ず失敗
    if (!$auth_request) {
      return false;
    }

    
    // 承認の為リダイレクト
    if ($auth_request->shouldSendRedirect()) {
      $redirect_url = $auth_request->redirectURL($trust_root, $process_url);

      // If the redirect URL can't be built, display an error
      // message.
      if (Auth_OpenID::isFailure($redirect_url)) {
        displayError("Could not redirect to server: " . $redirect_url->message);
      } else {
        // Send redirect.
        header("Location: ".$redirect_url);
        exit;
      }
    }
    else{
      $form_html = $auth_request->htmlMarkup( $trust_root, $process_url, false, array('id'=>'openid_message') );
      if( Auth_OpenID::isFailure( $form_html ) ){
        exit('error');
      }
      print $form_html;
      exit;
    }
  }

  /**
   * 認証結果判別
   *
   * @return bool 認証可否
   */
  public function checkResult() {
    $scheme = 'http';
    $scheme = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

    $this->_response = $this->_consumer->complete( sprintf(
        "$scheme://%s:%s%s",
        $_SERVER['SERVER_NAME'],
        $_SERVER['SERVER_PORT'],
        $_SERVER['REQUEST_URI']
        ));
    // 認可時のみ値セット
    if ($this->_response->status == Auth_OpenID_SUCCESS) {
      $this->_valid = true;
      $this->_contents = Auth_OpenID_SRegResponse::fromSuccessResponse($this->_response)->contents();
      $id = ($this->_response->endpoint->canonicalID) ? $this->_response->endpoint->canonicalID : $this->_response->identity_url;
    }
    return $this->isValid();
  }

  /**
   * @var bool 認可結果
   */
  public function isValid() {
    return $this->_valid;
  }

  /**
   * @return array 取得情報
   */
  public function getContents() {
    return $this->_contents;
  }
}
