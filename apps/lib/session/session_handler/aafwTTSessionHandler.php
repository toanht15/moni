<?php
AAFW::import ( 'jp.aainc.aafw.db.aafwTokyoTyrant' );
/**
 * ストア先のサーバーにConnectionを張る
 **/
function session_TT_open( $save_path, $session_name ){
  return true;
}

/**
 * ストア先のサーバーのConnectionを削除する
 **/
function session_TT_close(){
  return true;
}

/**
 * データを取り出して、そのまま返却する
 **/
function session_TT_read( $session_id ){
  $tt = aafwTokyoTyrant::getInstance('session');
  return $tt->get('session_' . $session_id );
}

/**
 * ストア先に、$session_idで$dataを保存する
 **/
function session_TT_write( $session_id, $data ) {
  $tt = aafwTokyoTyrant::getInstance('session');
  $tt->put( 'session_' . $session_id, $data );
}

/**
 * ストア先から$session_idのデータを削除する
 **/
function session_TT_destroy( $session_id ) {
  $tt = aafwTokyoTyrant::getInstance('session');
  $tt->put('session_' . $session_id, null );
}

/**
 * ストアされたデータから$max_life_timeに従って、有効期限切れのデータを削除する
 **/
function session_TT_gc( $max_life_time ) {
  // ★未実装
  // ちゃんと実装しろ
}
session_set_save_handler(
  "session_TT_open",
  "session_TT_close",
  "session_TT_read",
  "session_TT_write",
  "session_TT_destroy",
  "session_TT_gc"
  );
