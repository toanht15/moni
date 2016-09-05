<?php
AAFW::import ( 'jp.aainc.models.SessionStore' );
/**
 * ストア先のサーバーにConnectionを張る
 **/
function session_DB_open( $save_path, $session_name ){
  return true;
}

/**
 * ストア先のサーバーのConnectionを削除する
 **/
function session_DB_close(){
  return true;
}

/**
 * データを取り出して、そのまま返却する
 **/
function session_DB_read( $session_id ){
  $ss = new SessionStore();
  if ( $ss->find ( array ( 'session_id' => $session_id ) ) ) return $ss->data;
  else                                                       return null;
}

/**
 * ストア先に、$session_idで$dataを保存する
 **/
function session_DB_write( $session_id, $data ) {
  $ss = new SessionStore();
  $ss->find ( array ( 'session_id' => $session_id ) );
  $ss->session_id = $session_id;
  $ss->data       = $data;
  $ss->save();
}

/**
 * ストア先から$session_idのデータを削除する
 **/
function session_DB_destroy( $session_id ) {
  $ss = new SessionStore();
  if ( $ss->find ( array ( 'session_id' => $session_id ) ) ) $ss->delete();
}

/**
 * ストアされたデータから$max_life_timeに従って、有効期限切れのデータを削除する
 **/
function session_DB_gc ( $max_life_time ) {
  /** 未実装 **/
}

session_set_save_handler(
  "session_DB_open",
  "session_DB_close",
  "session_DB_read",
  "session_DB_write",
  "session_DB_destroy",
  "session_DB_gc"
  );
