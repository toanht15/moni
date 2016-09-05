<?php
/**
 * @package AAFramework
 * @subpackage Mysql
 * @author Akira Takahashi
 */
AAFW::import ( 'jp.aainc.aafw.db.DriverInterface' );
AAFW::import ( 'jp.aainc.aafw.db.DBException' );

/*
 * Mysql接続オブジェクト
 */
class mysql implements DriverInterface{

  public $server;
  public $port;
  public $database;
  public $user;
  public $password;

  public $connection;
  private $separate_connection = false;

  public function __construct($db_info){
    $this->server   = $db_info['server'];
    $this->user     = $db_info['user'];
    $this->password = $db_info['password'];
    $this->database = $db_info['database'];
    $this->separate_connection = $db_info['separate_connection'];
  }

  public function connect(){
    if ( isset( $this->connection ) ) return;
    if ( $this->separate_connection )  $this->connection = mysqli_connect ( $this->server, $this->user, $this->password, $this->database );
    else                               $this->connection = mysqli_connect ( $this->server, $this->user, $this->password, $this->database );

    if(!$this->connection){
      $errMsg  = "Can't Connect DB :" . $this->server;
      $errMsg .= " MySQL Message:"  . $this->getError();
      throw new DBException ( $errMsg );
    }
    $this->execute ( 'SET NAMES UTF8;' );
    // $this->execute ( 'set autocommit = 1;');
    $this->execute ( "SET TIME_ZONE = '+9:00';" );
  }

  public function execute($sql, $params = NULL){
    if ( !isset ( $this->connection ) ) $this->connect();
    if ( is_array($params) ) {
      foreach ( $params as $key => $value ){
        $sql = str_replace( $key, $this->escape ( $value ), $sql );
      }
    }

    $ret = mysqli_query ( $this->connection, $sql );
    if ( !$ret ){
      $errMsg  = "Can't Execute Query:" . $sql;
      $errMsg .= " MySQL Message:"    . $this->getError();
      throw new DBException ( $errMsg );
    }
    return $ret;
  }

  public function executeMulti ( $sql, $params = NULL ) {
    if ( !isset ( $this->connection ) ) $this->connect();
    if ( is_array($params) ) {
      foreach ( $params as $key => $value ){
        $sql = str_replace( $key, $this->escape ( $value ), $sql );
      }
    }

    $ret = mysqli_multi_query ( $this->connection, $sql );
    if ( !$ret ){
      $errMsg  = "Can't Execute Query:" . $sql;
      $errMsg .= " MySQL Message:"    . $this->getError();
      throw new DBException ( $errMsg );
    }
    do{
        /* 最初の結果セットを格納します */
        if ($result = mysqli_store_result($this->connection)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($this->connection));
    return $ret;
  }

  public function getError () {
      return "error no=" . mysqli_errno( $this->connection ) . ", sql state=" . mysqli_sqlstate( $this->connection ) . ", error=" . mysqli_error ( $this->connection );
  }

  public function fetch($resultSet){
    // 遅くは無いかも知れないけど名前でしかアクセスしないしメモリ勿体無いよね？
    // return mysqli_fetch_array($resultSet);
    return mysqli_fetch_assoc($resultSet);
  }

  public function begin( $isola_level = -1 ){

    if(!isset($this->connection)) $this->connect();
    if ( $isola_level > -1 ) {
      $isola_dic = array ( 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE');
      $sql = 'SET SESSION TRANSACTION ISOLATION LEVEL ' . $isola_dic[$isola_level];
      $this->execute( $sql );
    }
    //$sql = "set autocommit = 0;";
    //$this->execute($sql);
    $this->execute ( "begin;" );
  }

  public function commit(){
    if(!isset($this->connection)) $this->connect();
    $this->execute( "commit;" );
    $this->execute( "set autocommit = 1;" );
  }

  public function rollback(){
    if(!isset($this->connection)) $this->connect();
    $this->execute( "rollback;" );
    $this->execute( "set autocommit = 1;" );
  }

  public function getTableInfo($table_name){
    if(!isset($this->connection)) $this->connect();
    $ret = array();
    $rs = $this->execute( "SHOW COLUMNS FROM $table_name");
    while ( $row = $this->fetch ( $rs ) ){
      $temp['column']  = $row['Field'];
      //type(string, text, integer, decimal, float, datetime, date, binary, boolean)
      preg_match('/([^(]+)\(?(\d*),?(\d*)\)?/i', $row['Type'], $matches);
      $patterns                 = array('/varchar|char/i', '/text|mediumtext/i', '/int|integer|bigint/i', '/decimal|dec|numeric/i', '/float|double/i', '/datetime|timestamp/i', '/date/i', '/blob|mediumblob|lognblob/i', '/tinyint|bit|bool|boolean/i');
      $replacements             = array('string', 'text', 'integer', 'decimal', 'float', 'datetime', 'dateZ', 'binary', 'boolean');
      $temp['type']             = preg_replace($patterns, $replacements, $matches[1]);
      $temp['type_org']         = $matches[1];
      $temp['type_name']        = $row['Type'];
      $temp['length']           = $matches[2];
      $temp['length_decimally'] = $matches[3];
      $temp['extra']            = $row['Extra'];
      $temp['nullable']         = $row['Null'] == 'YES';
      $temp['key']              = $row[Key] == 'PRI';
      $temp['index']            = $row[Key] != '';
      $ret[$temp[column]] = $temp;
    }
    return $ret;
  }

  public function getTables () {
      $result = array ();

      $rs = $this->execute ( 'SHOW TABLES;');
      while ( $row = $this->fetch ( $rs )  ) {
          $result[] = array_shift ( $row );
      }
      return $result;
  }

  public function getIndexInfo ( $tableName ) {
    if(!isset($this->connection)) $this->connect();
    $ret = array();
    $rs = $this->execute( "SHOW INDEX FROM $tableName");
    while ( $row = $this->fetch ( $rs ) ){
        $ret[$row['Key_name']][] = $row;
    }
    return $ret;
  }

  public function clearDatabase () {
      try {
          $this->execute ( "DROP DATABASE $this->database;" );
          $this->execute ( "CREATE DATABASE $this->database;" );
          $this->execute ( "use $this->database;" );
      }
      catch ( Exception $e ) {
          var_dump ( $e );
          foreach ( $this->getTables () as $table ) {
              $table = $this->escape ( $table );
              $this->execute ( "DROP TABLE $table;" );
          }
      }
  }




  public function escape($value){
    if(!isset($this->connection)) $this->connect();
    return mysqli_real_escape_string( $this->connection, $value );
  }

  public function getAffectedRows () {
    return mysqli_affected_rows ( $this->connection );
  }

  public function getSelectedRows ( $resultSet ) {
    return mysqli_num_rows ( $resultSet );
  }

  public function setIndex ( $resultSet, $row_num ) {
    return mysqli_data_seek ( $resultSet, $row_num );
  }
}

