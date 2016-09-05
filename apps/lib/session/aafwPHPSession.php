<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwSessionBase' );
AAFW::import ( 'jp.aainc.aafw.web.aafwMobileDispatcher' );
AAFW::import ( 'jp.aainc.aafw.session.persistent_session.RedisPersistentSession' );
class aafwPHPSession extends aafwSessionBase{

  public function __construct($Config){

    $sessionTime = $Config->SessionTime;
    if ( is_numeric ( $sessionTime ) || preg_match( '#^\d+[YMWDHIS]$#i', $sessionTime )  ){
      $this->setSessionTime($sessionTime);
    }
    if ( $Config->MobileSessionHandler ) {
      $def = aafwMobileDispatcher::isMobile( $_SERVER ) ;
      if     ( $def['is_mobile'] )               require dirname( __FILE__ ) . '/session_handler/' . $Config->MobileSessionHandler  . '.php';
      elseif ( $Config->DefaultSessionHandler )  require dirname( __FILE__ ) . '/session_handler/' . $Config->DefaultSessionHandler . '.php';
    } elseif ( $Config->DefaultSessionHandler ) {
      require dirname( __FILE__ ) . '/session_handler/' . $Config->DefaultSessionHandler . '.php';
    }
    // PersistentSessionを確認する
    $redisPersistentSession = new RedisPersistentSession();
    $check = $redisPersistentSession->check();
    $this->start();
    // チェック失敗でトークンリセット、チェック成功でトークンはそのまま
    $redisPersistentSession->setSessionId(session_id(), !$check);
  }

  public function start(){
    session_start();
    if (!trim(session_id())) session_regenerate_id(true);
  }

  public function setSessionTime($sessionTime){
    if( preg_match( '#^\d+[YMWDHIS]$#i', $sessionTime ) ){
      ini_set( "session.gc_maxlifetime", $this->convertSecond( $sessionTime ) );
      session_set_cookie_params( $this->convertSecond( $sessionTime ) );
    }
    elseif ( is_numeric ( $sessionTime ) ) {
      ini_set( "session.gc_maxlifetime", $sessionTime );
      session_set_cookie_params( $sessionTime  );
    }
  }

  public function __set( $key, $value ){
    $_SESSION[$key] = $value;
  }

  public function __get( $key ){
    return $_SESSION[$key];
  }
  public function clear(){
    $_SESSION = array();
  }

  public function getValues(){
    return $_SESSION;
  }

  public function setValues( $val ){
    $_SESSION = $val;
  }
}
