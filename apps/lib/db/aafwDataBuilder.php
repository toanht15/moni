<?php
AAFW::import ( 'jp.aainc.aafw.db.DB' );
AAFW::import ( 'jp.aainc.aafw.db.aafwSQLEditor' );
AAFW::import ( 'jp.aainc.aafw.aafwApplicationConfig' );
class aafwDataBuilder {

  private $SQLDictionary = array();
  private $DataDriver    = null;
  private $SQLHistory    = array();
  private $db_list       = array();
  private $isObjectMode  = false;
  private $_entityFactory;

  /**
   * Masterに対してクエリを投げる、デフォルトのビルダーのインスタンスを生成します。
   * @return aafwDataBuilder
   */
  public static function newBuilder($db_name = null) {
    return new aafwDataBuilder($db_name, 'w');
  }

  /**
   * 特別な理由がない限りは、代わりにnewBuilder関数経由で取得してください。
   * @deprecated
   */
  public function __construct( $db_name = null , $db_type = null ){
    $dir_path = str_replace( '${AAFW_DIR}', AAFW_DIR ,  aafwApplicationConfig::getInstance()->DB['SQLLib'] );
    if ( $db_name ) {
      foreach( DB::getInstance( $db_name ) as $db ) $this->db_list[$db->db_id] = $db;
    } else {
      foreach( DB::getInstance( DB::getDefaultDBGroup() ) as $db ) $this->db_list[$db->db_id] = $db;
    }
    if( !is_dir( $dir_path ) ) throw new Exception( 'SQL文の入ったディレクトリが有りません' . $dir_path );
    $d = opendir( $dir_path );
    if ( !$db_type ) $this->DataDriver = $this->db_list['r'];
    else             $this->DataDriver = $this->db_list[$db_type];
    while( $f = readdir( $d ) ){
      if( !preg_match( '#\.sql$#', $f ) ) continue;
      $this->SQLDictionary[preg_replace( '#\.sql$#', '', $f )] = $dir_path . '/' . $f;
    }
    closedir( $d );
  }

  /**
   * SQL文の実行ログを返す
   * @return 実行ログ
   */
  public function getHistory(){
    return $this->SQLHistory;
  }

  /**
   * そのSQL文が設定されているかどうか
   * @param SQL文名
   * @return 設定されていればtrue, なければfalse
   */
  public function isDefine( $method ){
    return isset( $this->SQLDictionary[$method] );
  }

  /**
   * 実行結果をstdClassで返すかどうか
   * @param
   * @return void
   */
  public function setObjectMode( $flg ){
    $this->isObjectMode = $flg;
  }

  /**
   * SQL文をパラメータ置換して
   * @param SQL文の名前
   * @param 引数
   * @return getBySQLの実行結果
   */
  public function __call( $name, $args ) {
    if( $sql = file_get_contents( $this->SQLDictionary[$name] ) ){

      return $this->getBySQL("/* $name */ " . $sql, $args );
    }

    $class = $this->convertName( $name );
    if( is_file( $fn = AAFW_DIR . '/models/' .  $class . '.php' ) ) return $this->getObject( $class, $args );
    throw new Exception( 'そんなメソッドはありません' );
  }

  /**
   * SQL文を渡して実行する
   * @parma SQLテンプレート
   * @param SQL置換パラメータ他
   * @return array ( 'list' => 実行結果の配列, 'pager' => array ( 'max_page' => 最大ページ, 'count' => 総件数 )
   */
  public function getBySQL( $sql, $args ){
    $res = array();
    $error = null;
    list( $params, $order, $pager, $with_pager, $class ) = $args;
    $db = $this->DataDriver;
    $sql_editor = new aafwSQLEditor();
    if( !$params ) $params= array();
    if( !$order ) $order = array();
    $is_null_pager = false;
    if( !$pager ) {
      $is_null_pager = true;
      $pager = array();
    }

    foreach( split( "\n", $sql ) as $row ) $sql_editor->add( $row );
    foreach( $params as $key => $val )     $sql_editor->setParam( $key, $val );
    $sql = $sql_editor->toSQL();
    if (SQL_DEBUG_LOGGING) {
      aafwLog4phpLogger::getSQLLogger()->debug(json_encode(debug_backtrace()) . $sql);
    }
    try {
      if (!$is_null_pager) {
        $rs = $db->execute("select count(*) as c from ( $sql ) q");
        $row = $db->fetch($rs);
        $res['pager']['count'] = $row['c'];
      }

      if( $pager['count'] )           $res['pager']['max_page'] = ceil( $res[0]['c'] / $pager['count'] );
      if( $pager['page'] == 'first' ) $pager['page'] = '1';
      if( $pager['page'] == 'last'  ) $pager['page'] = $res['pager']['max_page'];

      $sql .= $sql_editor->getOrder( $order );
      $sql .= $sql_editor->getPager( $pager );

      $res['list'] = array();
      $rs = $db->execute($sql);
      if ( $params['__NOFETCH__'] ) {
        $res['list']['resource'] = $rs;
        $res['list']['class']    = $class;
      } else {
        while( $row = $db->fetch( $rs ) ){
          if( $class ){
            $obj = $this->getEntityFactory()->create($class);
            $obj->setValues( $row );
            $res['list'][] = $obj;
          } elseif( $this->isObjectMode ){
            $res['list'][] = ( object ) $row;
          } else {
            $res['list'][] = $row;
          }
        }
      }
    } catch( Exception $e ){
      $error = $e;
      $msg = "SQL execution failed!: SQL=" . $sql . ", message=" . $e->getMessage() . ", stack trace=" . json_encode(debug_backtrace(), JSON_PRETTY_PRINT);
      aafwLog4phpLogger::getHipChatLogger()->error($msg);
      aafwLog4phpLogger::getDefaultLogger()->error($msg);
      $res = array();
    }
    if( count( $this->SQLHistory ) > 5 ) array_shift( $this->SQLHistory );
    $this->SQLHistory[] = array(
        'error' => $error ,
        'sql' => $sql,
        'params' => array(
            'params' => $params,
            'order'  => $order,
            'page'   => $page
        ));
    if ($error !== null) {
      $msg = "SQL execution has failed!: history=" . json_encode($this->SQLHistory, JSON_PRETTY_PRINT);
      aafwLog4phpLogger::getDefaultLogger()->warn($msg);
    }
    if( $with_pager ) return $res;
    else              return $res['list'];
  }

  /**
   * ORマッパを検索して
   * @param $rs array ( 'resource' => りそーす, 'class' => クラス名 )
   * @return フェッチした結果
   */
  public function getObject( $class, $args ){
    if( is_file( $fn = AAFW_DIR . '/models/' .  $class . '.php' ) ) {
      require_once $fn;
    }
    elseif ( is_file( $fn = AAFW_DIR . '/classes/' .  $class . '.php' )  ){
      require_once $fn;
    }
    else {
      throw new Exception( 'classがありません' );
    }
    $obj = new $class;
    list( $params, $order, $pager ) = $args;
    if( is_array( $params ) ) {
      return $obj->find( array(
          'conditions' => $params,
          'order'      => $order['name'] . ' ' .  $order['direction'],
          'limit'      => $pager['count'],
          'offset'     => ( ( $pager['page'] - 1 ) * $pager['count'] ),
      ));
    } else {
      $obj->find( $params );
      return $obj;
    }
  }

  /**
   * フェッチする
   * @param $rs array ( 'resource' => りそーす, 'class' => クラス名 )
   * @return フェッチした結果
   */
  public function fetch ( $rs )  {
    $arr = $this->DataDriver->fetch ( $rs['resource'] );
    if ( !$arr ){
      return false;
    } elseif ( $rs['class'] ) {
      $obj = new $rs['class'];
      $obj->setValues ( $arr );
      return $obj;
    } elseif ( $this->isObjectMode ) {
      return ( object ) $arr;
    } else {
      return $arr;
    }
  }

  private function convertName( $str ){
    $ret = '';
    for( $i = 0; $i < strlen( $str ); $i++ ){
      if( !$i ) {
        $ret .= strtolower( $str[$i] );
      } else {
        if( preg_match( '#[A-Z]#', $str[$i] ) ) $ret .= '_';
        $ret .= strtolower( $str[$i] );
      }
    }
    return $ret;
  }

  public function executeUpdate($sql) {
    return $this->DataDriver->execute($sql);
  }

  public function executeMulti($sql) {
    return $this->DataDriver->executeMulti($sql);
  }

  public function fetchResultSet($rs) {
    return $this->DataDriver->fetch($rs);
  }

  public function getAffectedRows() {
    return $this->DataDriver->getAffectedRows();
  }

  public function escape($value) {
    return $this->DataDriver->escape($value);
  }

  private function getEntityFactory(){
    if(!$this->_entityFactory) {
      $this->_entityFactory = new aafwEntityFactory();
    }
    return $this->_entityFactory;
  }
}