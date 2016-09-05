<?php
/**
 * ユニットテストもどき支援クラス
 * @package org.fww.test
 * @access abstract
 * @author t_ishida
 **/
require_once 'aafwApplicationConfig.php';
class aafwTest {
  private $Name   = '';
  private $Props  = array();
  private $Report = array(
    'counter' => array(
      'all' => 0 ,
      'ok' => 0,
      'fail' => 0 ,
      ));
  private $Objects = array();
  /**
   * コンストラクタ
   * @param テスト名
   * @param プロパティ(省略可) というより現状は使わない
   **/
  public function __construct( $name, $props = array() ){
    $this->Name  = $name;
    $this->Props = $props;
  }

  public function prepareDatabase( $test_data_name  ){
    $fn = '';
    if ( !is_array( $this->Props['test-data'][$test_data_name] ) ){
      if ( preg_match( '#^(.+)/\*$#', $this->Props['test-data'][$test_data_name], $tmp ) ) {
        if ( is_dir( $dir = AAFW_DIR . '/t/data/' . $tmp[1] ) ){
          $this->Props['test-data'][$test_data_name] = array();
          $dh = opendir( $dir );
          while ( $fn = readdir( $dh ) )
            if ( preg_match( '#\.csv$#', $fn ) )
              $this->Props['test-data'][$test_data_name][] = $tmp[1] . '/' . $fn;
        } else {
          throw new Exception( 'ディレクトリの指定が不正です@' . $dir );
        }
      } else {
        $this->Props['test-data'][$test_data_name] = array( $this->Props['test-data'][$test_data_name] );
      }
    }
    if ( !aafwApplicationConfig::getInstance()->DBInfo['dummy'] )  throw new Exception( 'DBInfoにdummyグループを作成してください' );
    aafwApplicationConfig::reload();
    
    foreach ( $this->Props['test-data'][$test_data_name] as $fn ){
      if( !is_file( $fn = AAFW_DIR . '/t/data/' . $fn  ) ) {
        var_Dump ( $this->Props['test-data']);
        throw new Exception( 'テストデータの指定が不正です:' . $fn );
      }
      if ( preg_match( '#^(.+?)-#', basename( $fn ), $tmp )|| preg_match( '#^(.+?)\.csv#', basename( $fn ), $tmp ) ){
        if ( is_file( $class_file = AAFW_DIR . '/models/' . $tmp[1] . '.php' ) ){
          require_once $class_file;
          $obj = new $tmp[1];
          $obj->createTableTo( 'dummy' );
          $obj->loadCSVTo( $fn, 'dummy' );
          $this->Objects[] = $obj;
        }
      }
    }
    $obj = aafwApplicationConfig::getInstance();
    $arr = $obj->DBInfo;
    foreach ( $arr as $key => $value ) $arr[$key] = $arr['dummy'];
    //$obj->DBInfo = $arr;
    $obj->DBInfo = array ( 'dummy'    => $obj->DBInfo['dummy'], 'table_id' => $obj->DBInfo['dummy']  );
    aafwApplicationConfig::__setInstance( $obj );
    DB::reloadConfig ();
    return $this;
  }
  
  /**
   * スタート(ヘッダの書き出し及び、Windowsならob_startもする)
   **/
  public function start(){
    if( preg_match( '#WIN#', PHP_OS ) ) ob_start();
    // ダミーに接続するように強制的にconfigを上書き
    print "<< TEST:$this->Name  start >>\n";
  }

  /**
   * 渡された値が真かどうかだけ(基本これだけしか使わんはず)
   * @param 真偽値
   * @param テスト名(省略可)
   **/
  public function ok( $val, $case_name = '' ){
    $this->Report['counter']['all'] += 1;
    if( $case_name ) print $this->Report['counter']['all'] . ':' . $case_name . ':';
    else             print $this->Report['counter']['all'] . ':';
    if( $val ){
      $this->Report['counter']['ok'] += 1;
      print 'ok test ....OK' . "\n";
    } else {
      $this->Report['counter']['fail'] += 1;
      print 'ok test ....NG' . "\n";
    }
  }
  
  /**
   * 渡された値が偽かどうかだけ(基本これだけしか使わんはず)
   * @param 真偽値
   * @param テスト名(省略可)
   **/
  public function ng( $val, $case_name = '' ){
    $this->Report['counter']['all'] += 1;
    if( $case_name ) print $this->Report['counter']['all'] . ':' . $case_name . ':';
    else             print $this->Report['counter']['all'] . ':';
    if( !$val ){
      $this->Report['counter']['ok'] += 1;
      print 'ng test ....OK' . "\n";
    } else {
      $this->Report['counter']['fail'] += 1;
      print 'ng test ....NG' . "\n";
    }
  }
  
  /**
   * 同値検査
   * @param 比べる値
   * @param 比べられる値
   * @param テスト名(省略可)
   **/
  public function isOK( $val1, $val2, $case_name = '' ){
    $this->Report['counter']['all'] += 1;
    if( $case_name ) print $this->Report['counter']['all'] . ':' . $case_name . ':';
    else             print $this->Report['counter']['all'] . ':';
    
    if( $val1 == $val2 ){
      $this->Report['counter']['ok'] += 1;
      print 'isOK....OK' . "\n";
    } else {
      $this->Report['counter']['fail'] += 1;
      print 'isOK....NG val1=' . $val1 . ' val2=' . $val2 . "\n";
    }
  }
  
  /**
   * ちゃんとクラスが合ってるかどうか
   * @param オブジェクト
   * @param クラス名
   * @param テスト名(省略可)
   **/
  public function isClassOK( $val1, $val2, $case_name = ''){
    $this->Report['counter']['all'] += 1;
    if( $case_name ) print $this->Report['counter']['all'] . ':' . $case_name . ':';
    else             print $this->Report['counter']['all'] . ':';
    if (  get_class( $val1 ) == $val2 ){
      $this->Report['counter']['ok'] += 1;
      print 'isa_OK....OK' . "\n";
    } else {
      $this->Report['counter']['fail'] += 1;
      print 'isa_OK....NG val1=' . get_class( $val1 ). ' val2=' . $val2 . "\n";
    }
  }
  
  /**
   * 配列が同じものかどうか
   * @param 比べる配列
   * @param 比べられる配列
   * @param テスト名(省略可)
   **/
  public function isDeeplyOK( $val1, $val2, $case_name = '' ){
    $this->Report['counter']['all'] += 1;
    $str_val1 = '';
    $str_val2 = '';
    $val1 = $this->__Deep( create_function ( '$x', 'return (string)$x;' ) , $val1 ) ;
    $val2 = $this->__Deep( create_function ( '$x', 'return (string)$x;' ) , $val2 ) ;
    $str_val1 = var_export( $val1, true );
    $str_val2 = var_export( $val2, true );
    if( $case_name ) print $this->Report['counter']['all'] . ':' . $case_name . ':';
    else             print $this->Report['counter']['all'] . ':';
    
    if( $str_val1 == $str_val2 ){
      $this->Report['counter']['ok'] += 1;
      print 'isDeeplyOK....OK' . "\n";
    } else {
      $this->Report['counter']['fail'] += 1;
      print 'isDeeplyOK....NG' . "\n";
    }
  }
  
  /**
   * 配列を再帰的に遡ってlambda式を適用する
   **/
  private function __Deep( $fnc, $arg ){
    if ( is_array ( $arg ) ){
      for ( $i = 0; $i < count ( $arg ); $i++ ) $arg[$i] = $this->__Deep( $fnc, $arg[$i] );
    } else {
      $arg = $fnc ( $arg );
      if ( is_array ( $arg ) ) $arg = $this->__Deep ( $fnc, $arg );
    }
    return $arg;
  }

  /**
   * ラベル付きで区切り線を出力する
   **/
  public function printSeparateLine ( $label = null, $total_length = 50 ) {
    $len = strlen ( bin2hex ( $label ) ) / 2;
    for ( $i = 0; $i < ( $total_length - $len ) / 2; $i++ ) print '-';
    print $label;
    for ( $i = 0; $i < ( $total_length - $len ) / 2; $i++ ) print '-';
    print "\n";
  }

  /**
   * 終了してレポート
   **/
  public function end(){
    print "\n";
    print 'rep:' . $this->Report['counter']['ok'] . '/' . $this->Report['counter']['all'] . "  TOTAL....";
    print ( $this->Report['counter']['ok'] == $this->Report['counter']['all'] ? 'OK' : 'NG' ) . "\n";
    print "<< TEST:$this->Name  end >>\n";
    if( preg_match( '#WIN#', PHP_OS ) )  print mb_convert_encoding(  ob_get_clean(), 'sjis', 'utf8' );
    else                                 print ob_get_clean();
    foreach( $this->Objects as $obj ) $obj->dropTableTo( 'dummy' );
  }

  public function endNG( $e ){
    print "\n";
    print 'rep:' . $this->Report['counter']['ok'] . '/' . $this->Report['counter']['all'] . "  TOTAL....";
    if ( $e ){
      print "\n";
      print "uncaught exception :" . get_class ( $e ) . "\n";
      print "message :" . $e->getMessage()      . "\n";
    }
    print "<< TEST:$this->Name  end >>\n";
    if( preg_match( '#WIN#', PHP_OS ) )  print mb_convert_encoding(  ob_get_clean(), 'sjis', 'utf8' );
    else                                 print ob_get_clean();
    foreach( $this->Objects as $obj ) $obj->dropTableTo( 'dummy' );
  }

}
