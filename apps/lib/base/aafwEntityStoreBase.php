<?php
AAFW::import ( 'jp.aainc.aafw.db.DB' );
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );
AAFW::import ( 'jp.aainc.aafw.container.aafwEntityContainer' );
AAFW::import('jp.aainc.classes.CacheManager');
/**
 * DBに直接アクセスするDAOクラス
 */
class aafwEntityStoreBase extends aafwObject {
  protected $_EntityName       = null;
  protected $_StoreRead        = null;
  protected $_StoreMaster      = null;
  protected $_Catalog          = null;
  protected $_EntityFactory    = null;
  protected $_TableName        = null;
  protected $_DeleteFlagName   = 'del_flg';
  protected $_DateCreatedName  = 'created_at';
  protected $_DateModifiedName = 'updated_at';
  protected $_KeyList          = array ();
  protected $_keyType          = 0;
  protected $_DeleteType       = 0;

  protected static $_InTransaction = array();
  protected static $_CatalogList   = array();

  const KEY_TYPE_AUTOINCREMENT  = 0;
  const KEY_TYPE_UNAUTOSET      = 1;

  const DELETE_TYPE_LOGICAL     = 0;
  const DELETE_TYPE_PHYSICAL    = 1;

  // Transaction Isolation Levels.
  const TIL_READ_COMMITTED = 1;

  /**
   * コンストラクタ
   * @param
   */
  public function __construct ( $params = null ) {
    if ( is_array ( $params ) && $params['StoreRead'] && $params['StoreMaster'] ) {
      $this->_StoreRead     = $params['StoreRead'];
      $this->_StoreMaster   = $params['StoreMaster'];
    }
    elseif ( $params instanceof DB ) {
      $this->_StoreMaster = $this->_StoreRead = $params;
    }
    elseif ( is_null ( $params ) ) {
      return;
    }
    else {
      throw new aafwException ( 'パラメータの指定に問題があります' );
    }
    if ( $params['Config'] )         $this->setConfig ( $params['Config'] );
    if ( $params['TableName'] )      $this->_TableName     = $this->convertName ( $params['TableName'] );
    if ( $params['Catalog'] )        $this->_Catalog       = $params['Catalog'];
    if ( $params['EntityFactory'] )  $this->_EntityFactory = $params['EntityFactory'];
    if ( !$this->_TableName )        $this->_TableName     = $this->convertName ( get_class ( $this ) );

    $this->setEntityName  ( $params['EntityName'] );

    //テーブル情報を取得する
    if ( !$this->_Catalog ) {
      $table_name = $this->_TableName;
      if ( !isset ( self::$_CatalogList[$table_name] ) ) {
        $cache_manager = new CacheManager();
        $table_info = $cache_manager->getSchemaCache($this->_TableName);
        if ($table_info === false) {
          $table_info = $this->_StoreRead->getTableInfo($table_name);
          $cache_manager->setSchemaCache($this->_TableName, $table_info);
        }
        self::$_CatalogList[$table_name] = $table_info;
      }
      $this->_Catalog = self::$_CatalogList[$table_name];
    }

    //主キーリスト
    if( !$this->_KeyList ) {
      foreach ( $this->_Catalog as $column => $column_info ) {
        if( $column_info[key] ) $this->_KeyList[] = $column;
      }
    }

	if(SQL_DEBUG_LOGGING) {
	  $this->logger = aafwLog4phpLogger::getSQLLogger();
	}
  }

  public function setEntityName ( $param = null ) {
    if ( !$this->_EntityName )  $this->_EntityName = $param;
    if ( !$this->_EntityName )  $this->_EntityName = $this->convertManyToOne ( get_class ( $this ) );
  }

  public function getEntityName () {
    return $this->_EntityName;
  }

  public function createEmptyObject () {
    if ( !$this->_EntityName )
      throw new aafwException ( get_class( $this ) . 'には単数形クラスの指定がありません' );
    if ( !$this->_EntityFactory )
      throw new aafwException ( 'EntityFactoryが設定されていません' );
    return $this->_EntityFactory->create ( $this->_EntityName );
  }

  /**
   * テーブル名を設定する
   * @param テーブル名
   */
  public function setTableName ( $table_name ) {
    $this->_TableName = $table_name;
  }

  /**
   * テーブル名を取得する
   * @return テーブル名
   */
  public function getTableName () {
    return $this->_TableName;
  }

  /**
     * テーブル名を設定する
     * @param テーブル名
     */
    public function setCatalog ( $catalog ) {
        $this->_Catalog = $catalog;
        foreach ( $this->_Catalog as $column => $column_info ) {
            if( $column_info[key] ) $this->_KeyList[] = $column;
        }
    }

    /**
     * テーブル名を取得する
     * @return テーブル名
     */
    public function getCatalog () {
        return $this->_Catalog;
    }

  /**
   * 無条件に全レコードを取得します。
   */
  public function findAll() {
    return $this->find(array());
  }

    /**
   * レコードを取得する
   * @param params
   * @return
   */
  public function find ( $filter ) {
    if ($filter === null) {
      aafwLog4phpLogger::getHipChatLogger()->warn('The argument is null!: ' . json_encode(debug_backtrace()));
    }
    $on_master = false;
    if (isset($filter['on_master'])) {
      $on_master = $filter['on_master'];
      unset($filter['on_master']);
    }
    $sql =  $this->createSelectSQL ( $filter );
	$this->loggingSQL($sql);
    $res = null;
    $in_trans   = self::$_InTransaction;
    $for_update = $filter['for_update'];

    if ( $for_update || $in_trans || $on_master ) $res = $this->_StoreMaster->execute ( $sql );
    else                            $res = $this->_StoreRead->execute ( $sql );
    if ( !$this->_StoreRead->getSelectedRows ( $res ) ) return array ();
    return new aafwEntityContainer ( array (
      'EntityName'    => $this->_EntityName,
      'StoreMaster'   => $this->_StoreMaster,
      'StoreRead'     => $this->_StoreRead,
      'Config'        => $this->_Config,
      'EntityFactory' => $this->_EntityFactory,
      'ForUpdate'     => $for_update ,
      'InTrans'       => $in_trans,
      'Resource'      => $res,
    ));
  }

    /**
     * レコードを1件取得する
     * @param params
     * @return entity
     */
  public function findOne (  $filter ) {
    $result = $this->find ( $filter );
    if ( !$result ) return null;
    return $result->current();
  }

  /**
   * SELECT文を作成する
   * @param params
   * @return SELECT文
   */
  public function createSelectSQL ( $filter ) {
    $order         = null;
    $pager         = null;
    $for_update    = null;
        $join          = null;
    if ( is_array ( $filter ) ) {
      $order       = $filter['order'];
      $pager       = $filter['pager'];
      $for_update  = $filter['for_update'];
            $join        = $filter['join'];
      unset ( $filter['order'] );
            unset ( $filter['join'] );
      unset ( $filter['pager'] );
      unset ( $filter['for_update'] );
    }

        $sql = '';
        $sql  = 'SELECT ' . $this->createSelectBlock( $join ) . ' ';
        $sql .= 'FROM '   . $this->createFromBlock ( $join )  . ' ';

    if ( $tmp = $this->createFilters ( $filter ) )                       $sql .= $tmp;
    if ( $order && $tmp_order = $this->createOrderBySentece ( $order ) ) $sql .= ' ' . $tmp_order;
    if ( $pager && $tmp_pager = $this->getPager ( $pager ) )             $sql .= ' ' . $tmp_pager;
    if ( $for_update )                                                   $sql .= ' FOR UPDATE';
    return $sql;
  }

    public function createjoinParams ( $join ) {
        if ( !is_array ( $join ) )  $join = array ( 'name' => $join );
        if ( $join['name'] )        $join = array ( $join );
        for ( $i = 0; $i < count ( $join ); $i++ ) {
            if ( !$join[$i]['type'] )       $join[$i]['type']  = 'INNER';
            if ( !$join[$i]['alias'] )      $join[$i]['alias'] = $this->convertLowerStyle ( $join[$i]['name'] );
            if ( !$join[$i]['key'] )        $join[$i]['key']   = array (
                $this->_TableName . '.id' => $join[$i]['alias'] . '.' . $this->convertLowerStyle ( $this->convertManyToOne ( $this->_TableName ) ) . '_id'
            );
        }
        return $join;
    }

    public function createSelectBlock ( $join ) {
        $flds = array ();
        $prefix = $this->convertLowerStyle ( $this->convertOneToMany ( $this->_EntityName ) );
        foreach ( $this->_Catalog as $key => $val ) {
            $flds[] = $this->_TableName . '.' .  $key . ' as ' . $prefix . '_' . $key;
        }
        if ( $join ) {
            $join = $this->createjoinParams ( $join );
            foreach ( $join as $row ) {
                $flds[] = $row['alias'] . '.*';
            }
        }
        return join ( ',', $flds );
    }

    public function createFromBlock ( $join ) {
        $from = '';
        if ( $join ) {
            $join = $this->createjoinParams ( $join );
            $flds   = array ( $this->_TableName . '.*' );
            $tables = array ( $this->_TableName );
            foreach ( $join as $row ) {
                $tmp = $row['type'] . " JOIN " . $row['name'] . ' ' . $row['alias'] . " ON ";
                $keys = array ();
                foreach ( $row['key'] as $key => $val ) $keys[] = "$key = $val";
                $tmp .= join ( " AND ",  $keys );
                $tables[] = $tmp;
            }
            $from   = join ( ' ', $tables );
        }
        else {
            $from = $this->_TableName;
        }
        return $from;
    }


  public function createFilters ( $filter ) {
    $conditions    = null;
    $where         = null;
    $where_params  = null;
    if ( is_scalar ( $filter ) ) {
      $conditions = $filter;
    }
    elseif ( $filter['conditions'] ) {
      $conditions    = $filter['conditions'];
      $where         = $filter['where'];
      $where_params  = $filter['where_params'];
    }
    else {
      if ( $filter['where'] ) {
        $where = $filter['where'];
        unset ( $filter['where'] );
      }
      if ( $filter['where_params'] ) {
        $where_params = $filter['where_params'];
        unset ( $filter['where_params'] );
      }
      $conditions = $filter;
    }
    $tmp = array ();
      if ( $where ) {
        $tmp[] = '( ' . $this->createWhereFromParams ( $where, $where_params ) . ' )';
      } else{
        foreach ( $this->createWhereFromConditions ( $conditions ) as $elm ) {
          $tmp[] = $elm;
          if ($elm === null) {
            aafwLog4phpLogger::getHipChatLogger()->warn('The condition element is null!: ' . json_encode(debug_backtrace()));
          }
        }
      }
      return $tmp ? 'WHERE ' . join ( ' AND ', $tmp  ) : '';
  }


  /**
   * 指定された配列からWHERE句を作る
   * @param array ()
   * @return WHERE句
   */
  public function createWhereFromConditions ( $conditions ) {
    $filters  = array ();
    if( $this->_DeleteFlagName != '' and
        isset ( $this->_Catalog[$this->_DeleteFlagName] ) ) {
                $filters[] = "$this->_TableName.$this->_DeleteFlagName = '0'";
    }
    if ( !isset ( $conditions ) ) return $filters;
    if ( is_array ( $conditions ) ) {
      foreach($conditions as $key => $value){
        preg_match ( '/([^:]+):?([^:]*)/', $key, $matches );
        $column   = $matches[1];
        $operator = $matches[2];

        if ( !$operator ){
          if   ( is_array( $value ) ) $operator = 'IN';
          else                        $operator = '=';
        }

        if ( preg_match( '/IS\s+NULL|IS\s+NOT\s+NULL/i', $operator ) ) {
          $operand = '';
        }
        else {
          //if ( is_null ( $value ) ) continue;
          if ( is_array( $value ) ) {
            $buf      = array();
            $null_flg = 0;
            foreach( $value as $key ){
              if   ( strtoupper( $key ) == 'NULL' ) $null_flg = 1;
              else                                  $buf[]    = "'" . $this->escapeForSQL ( $key ) . "'";
            }
            if( $buf ) {
              $operand = '(' . join( ',', $buf ) . ') ' ;
              if ( $null_flg ) {
                $operand .= ' OR ' . $column . ' IS NULL ) ';
                $column   = '(' . $column;
              }
            }
            else {
              $operand  = '1';
              $operator = '=';
              $column   = '1';
            }
          }
          else {
            $operand = "'" . $this->escapeForSQL ( $value ) . "'";
          }
        }
                if ( !preg_match ( '#^\d+$#', $column )  && !preg_match ( '#\.#', $column ) ) $column = $this->_TableName . '.' . $column;
        $filters[] = "$column $operator $operand";
      }
    }
    elseif ( is_scalar ( $conditions ) ) {
      if ( count ( $this->_KeyList ) == 1 ) $filters[] = $this->_KeyList[0] . " = '" . $this->escapeForSQL ( $conditions ) . "'";
      else                                  throw new aafwException ( 'キーの数が足りません' );
    }
    else {
      throw new aafwException ( 'conditionsのパラメータが違います' );
    }

    return $filters;
  }

  /**
   * 指定されたWHERE句からパラメータの置換をする
   * @param WHERE句
   * @param パラメータ
   */
  public function createWhereFromParams ( $where, $where_params ) {
    if ( !$where ) return '';
    $sql = $where;

    if ( is_array( $where_params ) ) {
      foreach ( $where_params as $key => $value ) {
        $sql = str_replace ( "%$key%", "\x0b$key\x0b" , $sql );
      }
      foreach ( $where_params as $key => $value ) {
        if ( is_array ( $value ) ) {
          $vals = array();
                    foreach ( $value as $val ) $vals[] = "'" . $this->escapeForSQL ( str_replace ( "\x0b", '',  $val ) ) . "'";
          $sql = str_replace ( "\x0b$key\x0b", join ( ',', $vals ), $sql );
        } else {
                    $sql = str_replace ( "\x0b$key\x0b", $this->escapeForSQL ( str_replace ( "\x0b", '',  $value ) ), $sql );
        }
      }
    }
    return $sql;
  }

  /**
   * 指定された配列からORODE BY句を作る
   * @param mixin
   * @return ORDE BY 句
   */
  public function createOrderBySentece ( $order ) {
    $sql = '';
    if ( is_array( $order ) ){
      if ( $order['name'] ) $order = array( $order );
      else                  return ;
      $buf = array();
      foreach ( $order as $row ){
                if ( $row['name'] ) {
                    if ( !preg_match ( '#\.#', $row['name'] ) ) {
                        $row['name'] = $this->_TableName . '.' . $row['name'];
      }
                    $buf[] = $row['name'] . ' ' . ( $row['direction'] ?   $row['direction'] : 'asc' );
                }
            }
      $sql .= 'ORDER BY ' . join( ',', $buf ) . ' ';
    } else {
      if( trim( $order ) != ''){
        $sql .= 'ORDER BY ' . $order . ' ';
      }
    }
    return $sql;
  }

  /**
   * 指定ページを取得するためのLMIIT句を取得する
   * @param array ()
   * @return LIMIT句
   */
  public function getPager ( $pager ) {
    $sql = '';
    if ( is_array ( $pager ) ){
      if ( !$pager['count'] ) return '';
      if ( !$pager['page'] || !is_numeric ( $pager['page'] ) || $pager['page'] < 1 ) $pager['page'] = '1';
      $sql .= 'LIMIT ' . ( $pager['count'] * ( $pager['page'] - 1 ) ) . ',' . $pager['count'];
    } else {
      $sql = $pager;
    }
    return $sql;
  }

  /**
   * 保存する
   * @param 対象レコード
   */
  public function save ( $obj ) {
    if ( $this->isExistsOnMaster ( $obj ) ) {
      $sql =  $this->createUpdateSQL ( $obj ) ;
	  $this->loggingSQL($sql);
      $this->_StoreMaster->execute ( $sql );
    }
    else {
	  $sql = $this->createInsertSQL ( $obj );
	  $this->loggingSQL($sql);
      $this->_StoreMaster->execute ( $sql );
      $rs  = $this->_StoreMaster->execute( 'select last_insert_id() as id;' ) ;
      $row = $this->_StoreMaster->fetch( $rs );
      $obj->id = $row['id'];
    }
    return $obj;
  }

  /**
   * INSERT文を作成する
   * @param 対象レコード
   * @return INSERT文
   */
  public function createInsertSQL ( $obj ) {
    if ( is_array ( $obj ) ) $obj = ( object ) $obj;

    $columns = array ();
    $values  = array ();
    $need_set_key = false;
    if ( $this->_KeyType == self::KEY_TYPE_UNAUTOSET ){
      $need_set_key = true;
      foreach ( $this->_KeyList as $column ){
        if ( $obj->$column ) {
          $need_set_key = false;
          break;
        }
      }
    }

    if ( $need_set_key ) {
      foreach ( $this->getKeyValues () as $key => $value ) {
        $columns[] = $key;
        $values[]  = $value;
      }
    }

    foreach ( $this->_Catalog as $column => $column_info ){
      $column_value = $obj->$column;
      if ( $column_value ) $column_value = trim ( $column_value );
      if ( $column == $this->_DateCreatedName or $column == $this->_DateModifiedName ) {
        if ( $column_value == '' || $column_value == null )  $column_value = 'NOW()';
        else                                                 $column_value = "'" . $this->escapeForSQL ( date ( 'Y/m/d H:i:s', strtotime ( $column_value ) ) ) . "'";
      }
      elseif ( $column == $this->_DeleteFlagName ) {
        $column_value = "'0'";
      }
      elseif ( !is_null ( $column_value ) ) {
        $column_value = "'" . $this->escapeForSQL ( $column_value ) . "'";
      }
      else {
        continue;
      }
      $columns[] = $column;
      $values[]  = $column_value;
    }
    $sql = "INSERT INTO $this->_TableName ( " . join ( ',', $columns ) . " ) VALUES( " . join ( ',', $values ) . "); ";
    return $sql;
  }

  /**
   * UPDATE文を作成する
   * @param 対象レコード
   * @return UPDATE文
   */
  public function createUpdateSQL ( $obj ) {
    if ( is_array ( $obj ) ) $obj = (object) $obj;
    $elms     = array ();
    $old_keys = array ();
    foreach ( $this->_Catalog as $column => $column_info ){
      // PRIMARYカラムの更新は禁止する。
      if ( $column_info[key] ) {
        continue;
      }
      $val = $obj->$column;
      if     ( $column == $this->_DateModifiedName )  $val = 'NOW()';
      elseif ( strstr ( $val, "\x0bNULL\x0b" ) )      $val = 'null';
      elseif ( isset ( $val ) && !is_null ( $val ) )  $val = "'" . $this->escapeForSQL ($val ). "'";
      if ( $val ) $elms[] = "$column = $val";
    }
    $sql = "UPDATE $this->_TableName SET " . join ( ',', $elms ) . " " . $this->createThisWhere ( $obj );
    return $sql;
  }

  /**
   * 削除する
   * @param 対象オブジェクト
   */
  public function delete ( $obj ) {
    if ( $this->_DeleteType == self::DELETE_TYPE_LOGICAL ) $this->deleteLogical  ( $obj );
    else                                                   $this->deletePhysical ( $obj );
  }

  /**
   * 物理削除する
   * @param 対象オブジェクト
   */
  public function deletePhysical ( $obj ) {
    $sql =  "DELETE FROM $this->_TableName " . $this->createThisWhere ( $obj );
	$this->loggingSQL($sql);
    return $this->_StoreMaster->execute ( $sql );
  }

    public function truncate () {
        $sql =  "TRUNCATE TABLE $this->_TableName " ;
		$this->loggingSQL($sql);
        return $this->_StoreMaster->execute ( $sql );
    }

  /**
   * 論理削除する
   * @param 対象オブジェクト
   */
  public function deleteLogical ( $obj ) {
    $sql = "UPDATE $this->_TableName SET $this->_DateModifiedName = now(), $this->_DeleteFlagName = 1 " . $this->createThisWhere ( $obj );
	$this->loggingSQL($sql);
    return $this->_StoreMaster->execute ( $sql );
  }

  /**
   * 主キーで抽出するためのフィルターを作成する
   * @param 対象オブジェクト
   * @return WHERE句
   */
  public function createThisWhere ( $obj ) {
    $old_keys = $this->getKeys ( $obj );
    $tmp = $this->createWhereFromConditions ( $old_keys );
    if ( !$tmp ) throw new aafwException ( '条件がありません' );
    return ' WHERE ' . join ( ' AND ', $tmp );
  }

  /**
   * 対象オブジェクトから主キーのリストを取得する
   * @param 対象オブジェクト
   * @return WHERE句
   */
  public function getKeys ( $obj ) {
    $old_keys = array ();
    foreach ( $this->_Catalog as $column => $column_info ){
      if ( in_array ( $column, $this->_KeyList ) )  {
        if ( is_array ( $obj ) )            $value = $obj[$column];
        elseif ( $obj instanceOf stdClass ) $value = $obj->$column;
        else                     $value  = $obj->getOldValue ( $column );
        if ( is_null ( $value ) ) continue;
        $old_keys[$column] = $value;
      }
    }
    return $old_keys;
  }



  /**
   * 対象レコードの存在確認をする
   * @param 対象レコード
   * @return 存在すれば 1 以上
   */
  public function isExists ( $obj ) {
    $key = $this->getKey($obj);
    if ($key === 0) {
      return 0;
    } else {
      return $this->count($key);
    }
  }

  /**
   * 対象レコードのマスター上での存在確認をする
   * @param 対象レコード
   * @return 存在すれば 1 以上
   */
  public function isExistsOnMaster ( $obj ) {
    $key = $this->getKey($obj);
    if ($key === 0) {
      return 0;
    } else {
      return $this->countOnMaster($key);
    }
  }

  private function getKey($obj) {
    if ( $obj instanceof aafwEntityBase ) {
      $key = $this->getKeys ( $obj );
      if ( !$key ) {
        return 0;
      } else {
        return $key;
      }
    }
    elseif ( is_array ( $obj ) || is_scalar ( $obj ) ) {
      return $obj;
    }
    else {
      return 0;
    }
  }

  /**
   * 件数を取得する
   * @param 条件
   * @return 件数
   */
  public function count ( $filter, $col = null ) {
    if (self::$_InTransaction) {
      $store = $this->_StoreMaster;
    } else {
      $store = $this->_StoreRead;
    }
    return $this->doCount($store, $filter, $col);
  }

  /**
   * マスター上での件数を取得する
   * @param 条件
   * @return 件数
   */
  public function countOnMaster ( $filter, $col = null ) {
    return $this->doCount($this->_StoreMaster, $filter, $col);
  }

  private function doCount($store, $filter, $col = null) {
    if ( ! $store ) throw new Exception ( 'DBがありません' );
    $join          = null;
    if ( is_array ( $filter ) ) {
      $join        = $filter['join'];
      unset ( $filter['order'] );
      unset ( $filter['join'] );
      unset ( $filter['pager'] );
      unset ( $filter['for_update'] );
    }

    $uniq = time();
    if ( !$col ) $col = is_array ( $filter ) &&  $filter['__col_name'] ? 'DISTINCT ' . $filter['__col_name'] : '*';
    else         $col = "DISTINCT $col";
    if ( is_array ( $filter ) && $filter['__col_name'] ) unset ( $filter['__col_name'] );

    $sql = "SELECT count($col) as q_$uniq FROM " . $this->createFromBlock ( $join ) . " " . $this->createFilters ( $filter );
    $this->loggingSQL($sql);
    $row = $store->fetch ( $store->execute ( $sql ) );
    return $row["q_$uniq"];
}

  /**
   * 集計関数を実行する
   * @param 条件
   * @return 結果
   **/
  public function doFunctions ( $fname, $col_name , $conditions = null ) {
    $uniq = uniqid();
    $sql = "SELECT $fname(IFNULL(`$col_name`,0)) as tmp_$uniq FROM $this->_TableName " . $this->createFilters ( $conditions );
	$this->loggingSQL($sql);
    $row = $this->_StoreRead->fetch($this->_StoreRead->execute($sql));
    return $row["tmp_$uniq"];
  }

  /**
   * 平均を取得する
   * @param 条件
   * @return 平均
   **/
  public function getAverage ( $col_name , $conditions = null ) {
    return $this->doFunctions ( 'AVG', $col_name, $conditions );
  }

  /**
   * 最大値を取得する
   * @param 条件
   * @return 最大値
   **/
  public function getMax ( $col_name, $conditions = null ) {
    return $this->doFunctions ( 'MAX', $col_name, $conditions );
  }

  /**
   * 合計を取得する
   * @param 条件
   * @return 合計
   **/
  public function getSum ( $col_name, $conditions = NULL ) {
    return $this->doFunctions ( 'SUM', $col_name, $conditions );
  }

  /**
   * 最小値を取得する
   * @param 条件
   * @return 最小値
   **/
  public function getMin ( $col_name, $conditions = null ){
    return $this->doFunctions ( 'MIN', $col_name, $conditions );
  }

  /**
   * 標準偏差を取得する
   * @param 条件
   * @return 標準偏差
   **/
  public function getSTDDev ( $col_name, $conditions = NULL, $where = NULL, $where_params = NULL ){
    return $this->doFunctions ( 'STDDEV', $col_name, $conditions );
  }

  /**
   * 分散を取得する
   * @param 条件
   * @return 分散
   **/
  public function getVariance ( $col_name, $conditions = NULL, $where = NULL, $where_params = NULL ){
    return $this->doFunctions ( 'VARIANCE', $col_name, $conditions );
  }


  /**
   * トランザクションを開始する
   */
  public function begin ( $isola_level = -1 ) {
    if ( !$this->_StoreMaster ) throw new aafwException ('DBがありません');
    if ( !self::$_InTransaction[$this->_StoreMaster->db_group_id]++ ) $this->_StoreMaster->begin( $isola_level );
  }

  /**
   * トランザクションをコミットする
   */
  public function commit () {
    if ( !$this->_StoreMaster ) throw new aafwException ('DBがありません');
    if ( !self::$_InTransaction[$this->_StoreMaster->db_group_id] )    return ;
    if ( ! --self::$_InTransaction[$this->_StoreMaster->db_group_id] ) $this->_StoreMaster->commit();
  }


  /**
   * ロールバックする
   */
  public function rollback () {
    if ( !$this->_StoreMaster ) throw new aafwException ('DBがありません');
    if ( !self::$_InTransaction[$this->_StoreMaster->db_group_id] )    return ;
    if ( ! --self::$_InTransaction[$this->_StoreMaster->db_group_id] ) $this->_StoreMaster->rollback();
  }

  /**
   * 主キーをジェネレートする
   * @return 主キーのリスト
   */
  public function getKeyValues(){
    $buf = array ();
    for ( $i=0; $i<10; $i++ ){
      foreach ( $this->_KeyList as $column ){
        if     ( isset ( $this->_KeyLength ) and $this->_KeyLength <= $this->_Catalog[$column]['length'] ) $key_length = $this->_KeyLength;
        elseif ( $this->_Catalog[$column]['length'] != '' )                                                $key_length = $this->_Catalog[$column]['length'];
        else                                                                                               $key_length = 16;
        $buf[$column] = $this->getRandomString ( $key_length, $this->_Catalog[$column]['type'] );

      }
      //存在確認してOKならbreak
      if( !$this->isExists ( $buf ) ) break;
    }
    return $buf;
  }

  /**
   * ランダム文字列を作成する
   * @param 長さ
   * @param タイプ
   * @return ランダム文字列
   */
  public function getRandomString ( $length = 16, $type = 'string' ){
    $random_char = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$random_string = "";
    //文字列タイプの場合
    if($type == 'string'){
      mt_srand();
      for($i = 0; $i < $length; $i++){
        $random_string .= $random_char{mt_rand(0, strlen($random_char) - 1)};
      }
    }else{
      //数値タイプ
      mt_srand();
      $random_string = mt_rand(int('1' . str_repeat('0', $length - 1)), int('1' . str_repeat('9', $length)));
    }
    return $random_string;
  }


  /**
   * SQLエスケープする
   * @param 文字列
   * @return エスケープ後文字列
   */
  public function escapeForSQL ( $str ) {
        if ( !$this->_StoreRead ) {
            return str_replace ( "'", "''", $str );
        } else {
    return $this->_StoreRead->escape ( $str );
  }
}
  private function loggingSQL($sql) {
    if (SQL_DEBUG_LOGGING) {
	  $this->logger->debug(json_encode(debug_backtrace()));
	}
  }

  public static function loadCatalogs($table_names) {
    $cache_manager = new CacheManager();
    $table_info = $cache_manager->getSchemaCaches($table_names);
    foreach ($table_info as $name => $value) {
      if ($value) {
        self::$_CatalogList[$name] = $value;
      }
    }
  }

  public static function getCatalogs() {
    return self::$_CatalogList;
  }

  public static function newEntity($store_name, $properties = array()) {
    $store = aafwEntityStoreFactory::create($store_name);
    $new_entity = $store->createEmptyObject();
    foreach ($properties as $key => $val) {
      $new_entity->$key = $val;
    }
    return $new_entity;
  }

  public static function getInTransactions() {
    return self::$_InTransaction;
  }

  public static function clearInTransactions() {
    self::$_InTransaction = array();
  }
}

