<?php
AAFW::import ( 'jp.aainc.aafw.db.DB' );
class DBFactory {
  private static $_Instance = null;
  private $_DBList = array ();

  private function __construct () { }

  public static function getInstance () {
    if ( !self::$_Instance ) self::$_Instance = new DBFactory ();
    return self::$_Instance;
  }

  public function getDB ( $db_group_id = null ) {
    if ( !$db_group_id ) $db_group_id = DB::getDefaultDBGroup();
    if ( !$this->_DBList[$db_group_id] ) {
      $this->_DBList[$db_group_id] = new stdClass;
      foreach ( DB::getInstance ( $db_group_id ) as $db ){
        $key = $db->db_id;
        if     ( $key == 'w' ) $key = 'Master';
        elseif ( $key == 'r' ) $key = 'Read';
        $this->_DBList[$db_group_id]->$key = $db;
      }
    }
    return $this->_DBList[$db_group_id];
  }
}
