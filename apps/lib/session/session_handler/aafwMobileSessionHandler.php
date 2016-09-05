<?php
AAFW::import ( 'jp.aainc.aafw.db.aafwMemcacheManager' );
 /**
 * ストア先のサーバーにConnectionを張る
 **/
function session_mobile_open( $save_path, $session_name ){
  return true;
}

/**
 * ストア先のサーバーのConnectionを削除する
 **/
function session_mobile_close(){
  return true;
}
/**
 * データを取り出して、そのまま返却する
 **/
function session_mobile_read( $session_id ){
    $mm= aafwMemcacheManager::singleton();
    return serialize($mm->get('session_' . $session_id ));
}
/**
 * ストア先に、$session_idで$dataを保存する
 **/
function session_mobile_write( $session_id, $data ) {

  $mm= aafwMemcacheManager::singleton();
  $mm->set( 'session_' . $session_id, $data );
}

/**
 * ストア先から$session_idのデータを削除する
 **/
function session_mobile_destroy( $session_id ) {
    $mm= aafwMemcacheManager::singleton();
    $mm->delete('session_' . $session_id);
}
/**
 * ストアされたデータから$max_life_timeに従って、有効期限切れのデータを削除する
 **/
function session_mobile_gc( $max_life_time ) {
  // ★未実装
  //memcacheのexpire使うから一旦いいかな
}

session_set_save_handler(
  "session_mobile_open",
  "session_mobile_close",
  "session_mobile_read",
  "session_mobile_write",
  "session_mobile_destroy",
  "session_mobile_gc"
  );

?>
