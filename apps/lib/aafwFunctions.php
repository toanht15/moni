<?php
/**
 * println
 */
function say (){
  if ( !( $data = func_get_args() ) ) return;
  echo join ( ',', $data ) . ( php_sapi_name() == 'cli' ? "\n" : '<br>' );
}

/**
 * デバッグ用println
 */
function debug_print () {
  if ( !DEBUG ) return ;
  if ( !( $data = func_get_args() )) return ;
  say ( join ( ', ',  $data ) );
}

/**
 * デバッグ用dump
 */
function dump ( $s ) {
  if( !DEBUG ) return ;
  $arr = debug_backtrace();
  echo "############## DEBUG ##############<br />";
  echo '@@stack_trace' . "\n";
  echo '<ul>';
  foreach( $arr as $ln ){
    echo '<li>' . $ln['function'] . '@' . $ln['line'] . '@' . $ln['file'] . '<br />@@args:<ol>';
    foreach( $ln['args'] as $arg ) echo '<li>' . ( is_object( $arg ) ? 'object' : ( is_array( $arg ) ? 'array' : 'scalar' ) ). '</li>';
    echo '</ol></li>';
  }
  echo '</ul>';
  echo '@@value <br />';
  echo '<pre>'; var_dump( $s ); echo '</pre>';
  echo "############## /DEBUG ##############<br />";
}

///**
// * autload
// */
//function __autoload ( $class ) {
//  if     ( is_file ( $path = AAFW_DIR . '/' . 'models/'  . $class . '.php' ) ) include_once realpath ( $path );
//  elseif ( is_file ( $path = AAFW_DIR . '/' . 'classes/' . $class . '.php' ) ) include_once realpath ( $path );
//  elseif ( is_file ( $path = AAFW_DIR . '/' . 'vendor/'  . $class . '.php' ) ) include_once realpath ( $path );
//  else                                                                         {
//    if ( !@include_once  (  $class . '.php' ) ) throw new Exception ( $php_errormsg );
//  }
//}
/**
 * assignのエイリアス
 * @param 出力する変数
 */
function asign     ( $s ){
  assign( $s );
}

/**
 * HTMLをエスケープして出力する
 * @param 出力する変数
 */
function assign ( $s ) {
    echo htmlspecialchars( $s, ENT_QUOTES );
}

/**
 * HTMLをエスケープして出力する
 * @param 出力する変数
 */
function assign_str ( $s ) {
    return htmlspecialchars( $s, ENT_QUOTES );
}

/**
 * assign_urlのエイリアス
 * @param 出力する変数
 */
function asign_url ( $s ){
  echo assign_url ( $s );
}

/**
 * URLエンコードして出力する
 * @param 出力する変数
 */
function assign_url ( $s ){
  echo rawurlencode( $s );
}

//ﾓﾊﾞｲﾙ用に追加
function asign_url_mobile ( $s,$m, $p = null ){

  echo asign_url_mobile_str ( $s,$m, $p );
}

//ﾓﾊﾞｲﾙ用に文字列URL
function asign_url_mobile_str ( $s, $m, $p = null ){

  $str = "";
  $config = aafwApplicationConfig::getInstance();
  if ( preg_match ( '#^tel:#', $s ) ) {
    $str = $m['type'] == 'kddi' ? $s : preg_replace ( '#-#', '', $s ) ;
  } else {
    //モバイルの場合 snid を付与
    if( eregi( $config->Commerce['MobileDomain'] , $s ) or eregi( '^/' , $s ) )  {
      //すでにsnidが付いているときははずす。
      $s = preg_replace("/(^.*?)([\?&]snid=.*)/","$1",$s);
      //すでに guid=ON が付いているときははずす。
      $s = preg_replace("/(^.*?)([\?&]guid=ON)/","$1",$s);
      $str = $s;
      if( !trim ( $_COOKIE['PHPSESSID'] ) || $m['type'] == 'kddi' ){
        //snid付加
        if ( session_id() )    $str .= ( preg_match ( '#\?#', $str ) ? '&' : '?' ) . 'snid=' . session_id();
        if ( $m['mobile_id'] ) $str .= ( preg_match ( '#\?#', $str ) ? '&' : '?' ) . 'g2id=' . base64_encode($m['mobile_id']);
      }else{
        //ダミーID
        $str.= ( preg_match ( '#\?#', $str ) ? '&' : '?' ) . 'dmy='.time();
      }

      if($m['type']=='docomo') $str .= ( preg_match ( '#\?#', $str ) ? '&' : '?' ) . 'guid=ON';

      //絶対パス以外の場合、ドメインを付加
      if ( $p ) {
        $str = $p . '://'.$config->Commerce['MobileDomain'] . $str;
      }
      elseif( !(substr($str,0,7) == "http://" || substr($str,0,8) == "https://") ){
        if( $_SERVER['SERVER_PORT'] == '80' )
          $str = 'http://'.$config->Commerce['MobileDomain'] . $str;
        else
          $str = 'https://'.$config->Commerce['MobileDomain'] . $str;
      }

    }else{

      $str = 'http://'.$config->Commerce['MobileDomain'].'/'.$config->MobileGateWay.'?url='.rawurlencode( $s );

    }

  }
  return $str;
}

/**
 * write_html の エイリアス
 * @param 出力する変数
 */
function writeHTML ( $s ){
  write_html ( $s );
}

/**
 * HTMLとして出力する
 * @param 出力する変数
 */
function write_html( $s ){
  if     ( is_scalar ( $s ) ) echo $s;
  elseif ( !$s )              echo '';
  else                        var_dump ( $s );
}

/**
 * assign_jsのエイリアス
 * @param 出力する変数
 */
function asign_js  ( $s ){
  assign_js ( $s );
}

/**
 * JSの文字列中に適当な値として出力する
 * @param 出力する変数
 */
function assign_js  ( $s ){
  echo str_replace ( array ( '"', "'", '\\', "\n" ), array( '\\"', "\\'",'\\\\', '\n' ), $s );
}

function array_walk_deeply ( $v, $f ) {
  $is_obj = null;

  if ( is_object ( $v ) && $v instanceof stdClass ) {
    $is_obj = $v = (array)$v;
  }

  if ( is_array  ( $v ) ) {
    foreach ( $v as $key => $val ) {
      $v[$key] = array_walk_deeply ( $v[$key], $f  );
    }
  }
  elseif ( is_scalar ( $v ) ) {
    $v = $f ( $v );
  }

  if ( $is_obj ) $v = (object)$v;
  return $v;
}

/**
 * エラーログをwarningレベルで記録します。
 * @param $msg
 */
function log_error($msg) {
  aafwLog4phpLogger::getDefaultLogger()->warn($msg);
}

