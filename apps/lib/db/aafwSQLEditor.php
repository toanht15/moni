<?php
/***************************
 * SQL文作成支援クラス
 ***************************/
class aafwSQLEditor {
  private $SQL        = array();
  private $Parameters = array();
  private $AutoEscape = true;

  /***************************
   * コンストラクタ
   ***************************/
  public function __construct( $flg = true ){
    $this->AutoEscape = $flg;
  }
  
  /***************************
   * SQL文の行追加
   ***************************/
  public function add( $s ){
    foreach( split( "\n", $s ) as $ln ){
      $ln = preg_replace( '/\?(.+?)\?/', "\b$1\b", $ln );
      $ln = preg_replace( '/\s+/', ' ', $ln );
      $ln = preg_replace( '#--.+$#', '', $ln );
      if( !$ln ) continue;
      $this->SQL[] = $ln;
    }
    return $this;
  }
  
  /***************************
   * パラメータの追加
   ***************************/
  public function setParam( $key, $value ){
    if( is_array( $value ) ) {
      $tmp = array();
      foreach( $value as $val ){
        $tmp[] = $this->escape( $val );
      }
      $this->Parameters[$key] = join( ',', $tmp );
    } else {
      if( $value === '__ON__' ){
        $value = ' ';
      } else {
        $value = $this->escape( $value );
      }
      $this->Parameters[$key] = $value;
    }
    return $this;
  }
 
  /***************************
   * パラメータのクリア
   ***************************/
  public function clearParam(){
    $this->Parameters = array();
  }

  /***************************
   * 全部クリア
   ***************************/
  public function clearALL(){
    $this->SQL        = array();
    $this->Parameters = array();
  }
  
  /***************************
   * テンプレートをパースしてSQL文を返す
   ***************************/
  public function toSQL(){
    $ret = '';
    foreach( $this->SQL as $ln ){
      foreach( $this->Parameters as $key => $value ){
        if( is_null( $value ) ) continue;
        $ln = str_replace( "\b$key\b", $value, $ln );
      }
      if( strpos( $ln, "\b" ) ) continue;
      $ret = "$ret $ln";
    }
    return $ret;
  }
  
  /***************************
   * デバッグしやすい文字列を返す
   ***************************/
  public function getReport( ){
    $ret = '-- SQL文情報 --' . "\n";
    foreach( $this->SQL as $ln ){
      $ln = str_replace( '\b', '?', $ln  );
      $ret .= "\n$ln";
    }
    $ret .= "\n\n" . '-- パラメータ --' . "\n";
    foreach( $this->Parameters as $key => $value ) {
      $ret .= " $key => $value\n";
    }
    
    $ret .= "\n\n-- パース結果 -- \n";
    foreach( $this->SQL as $ln ){
      foreach( $this->Parameters as $key => $value ){
        if( is_null( $value ) ) continue;
        $ln = str_replace( "\b$key\b", $value, $ln );
      }
      if( strpos( $ln, "\b" ) ) continue;
      $ret = "$ret\n$ln";
    }
    
    return $ret;
  }

  /***************************
   * デストラクタ
   ***************************/
  function escape( $val ){
    if( is_null( $val ) ) return null;
    if( !is_numeric( $val ) ){
      if( get_magic_quotes_gpc() ) $val = stripslashes( $val );
      if( $this->AutoEscape )      $val = "'" . str_replace( array("'",'\\'), array("''",'\\\\'),  $val ) . "'";
    }
    return $val;
  }

  /************************************
   * ページャーを作る
   ************************************/
  public function getPager( $pager ){
    $sql = '';
    if( is_array( $pager ) && $pager['count'] ){
      if( !$pager['page'] ) $pager['page'] = 1;
      $page = $pager['page'] && preg_match( '/^\d+$/', $pager['page'] ) ? $pager['page'] : 1;
      $sql .= " limit " . $pager['count'] . " offset " . $pager['count'] *  ( $page - 1 );
    }
    return $sql;
  }

  /************************************
   * Order Byを作る
   ************************************/
  public function getOrder( $order ){
    if( !$order )  return '';
    $sql = '';
    !is_array( $order ) && $order = array( 'name' => $order, 'direction' => 'asc' );
    if( is_array( $order[0] ) ){
      $sql .= ' order by ';
      $buf  = array();
      foreach( $order as $h ) $buf[] =  $h['name'] . ' ' . $h['direction'];
      $sql .=  join( ',', $buf );
    }elseif( $order['name'] &&  $order['direction'] ){
      $sql .= ' order by ' . $order['name'] . ' ' . $order['direction'];
    }
    return $sql;
  }  
  
  /***************************
   * デストラクタ
   ***************************/
  public function __destruct( ){
    $this->clearALL();
  }
}
