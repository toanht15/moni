<?php
/**
 * @package AAFramework
 * @subpackage DB
 * @author Akira Takahashi
 */
AAFW::import ( 'jp.aainc.aafw.aafwApplicationConfig' );
AAFW::import ( 'jp.aainc.aafw.db.DBInterface' );
/*
 * データベースグループID　＋ データベースID（ない場合あり）につきシングルトン生成される
 */
class DB implements DBInterface {

    private static $db_collection = array();
    private static $settings = NULL;

    public	$db_group_id;
    public	$db_id;

    private	$db_info;
    private	$driver;

    private function __construct($db_group_id, $db_id){

        $this->db_group_id = $db_group_id;
        $this->db_id = $db_id;

        $this->parseDSN();
        $this->getDriver();


    }

    public function getDriverName () {
        return get_class ( $this->driver );
    }

    public static function loadConfig(){
        if(!isset(self::$settings)) self::$settings = aafwApplicationConfig::getInstance()->getValues();
    }

    public static function reloadConfig () {
        self::$settings = null;
        self::loadConfig ();
    }

    public static function getDBGroups () {
        return array_keys ( self::$settings['DBInfo'] );
    }

    //インスタンス生成
    public static function getInstance($db_group_id, $db_id = NULL) {

        $db_id_list = array();
        $ret = array();

        //データベースグループIDは必須
        if($db_group_id == '') return false;


        if($db_id != ''){
            //データベースIDが設定されていれば、それのみ取得（返り値はオブジェクト）
            $db_id_list[] = $db_id;
        }else{
            //データベースIDが設定されてなければグループ全部を取得(返り値はオブジェクトを格納した配列)
            $db_id_list = self::getDBinDBGroup($db_group_id);
        }

        $sep_con = in_array('separate_connection', $db_id_list);
        foreach($db_id_list as $db_id_temp){
            if ( $db_id_temp != 'r' && $db_id_temp != 'w' ) continue;
            //すでに生成済みならばそれを返す
            if(isset(self::$db_collection[$db_group_id][$db_id_temp])){
                $ret[] = self::$db_collection[$db_group_id][$db_id_temp];
            }else{
                self::$db_collection[$db_group_id][$db_id_temp] = new DB($db_group_id, $db_id_temp);
                $ret[] = self::$db_collection[$db_group_id][$db_id_temp];
            }


        }
        if($db_id != ''){

            return $ret[0];

        }else{

            return $ret;
        }

    }

    //デフォルトのDBグループを取得
    public static function getDefaultDBGroup(){

        self::loadConfig();

        foreach(self::$settings[DBInfo] as $key => $value){
            return $key;
        }

    }

    //DBグループに属するDBIDをリストで取得
    public static function getDBinDBGroup($db_group_id){
        self::loadConfig();

        $ret = array();

        foreach(self::$settings[DBInfo][$db_group_id] as $key => $value){
            $ret[] = $key;
        }
        return $ret;

    }

    private function parseDSN(){

        self::loadConfig();

        if($this->db_id != ''){
            $dsn = self::$settings[DBInfo][$this->db_group_id][$this->db_id];
        }else{
            $dsn = self::$settings[DBInfo][$this->db_group_id];
        }

        //preg_match('/([^:\/]+):\/\/([^:\/]+):?([^@]*)@([^\/]+)\/([^:\/]+)/', $dsn, $matches);
        preg_match('#([^:/]+)://([^:/]+):?([^@]*)@([^/]+)/([^:/]+)#', $dsn, $matches);
        $ret = array();
        if ( preg_match( '#^sep_(.+$)#', $matches[1], $tmp ) ){
            $ret['separate_connection'] = 1;
            $ret['dbtype'] = $tmp[1];
        } else {
            $ret[dbtype]  = $matches[1];
        }
        $ret[user]			= $matches[2];
        $ret[password]  = $matches[3];
        $ret[server]		= $matches[4];
        $ret[database]  = $matches[5];
        $this->db_info  = $ret;


    }

    private function getDriver(){
        AAFW::import ( 'jp.aainc.aafw.db.' . $this->db_info[dbtype] );
        $this->driver = new $this->db_info[dbtype]($this->db_info);
    }

    public function connect(){
        $this->driver->connect();
    }

    public function execute($sql, $params = NULL){
        return $this->driver->execute($sql, $params);
    }

    public function executeMulti($sql, $params = NULL){
        return $this->driver->executeMulti($sql, $params);
    }

    public function fetch($resultSet){
        return $this->driver->fetch($resultSet);
    }

    public function getResultRowCount($resultSet){
        return $this->driver->getResultRowCount($resultSet);
    }

    public function begin( $isola_level = -1 ){
        $this->driver->begin( $isola_level );
    }

    public function commit(){
        $this->driver->commit();
    }

    public function rollback(){
        $this->driver->rollback();
    }

    public function getTables(){
        return $this->driver->getTables();
    }

    public function clearDatabase(){
        return $this->driver->clearDatabase();
    }

    public function getTableInfo($table_name){
        return $this->driver->getTableInfo($table_name);
    }

    public function escape($value){
        return $this->driver->escape($value);
    }

    public function getAffectedRows(){
        return $this->driver->getAffectedRows();
    }

    public function getSelectedRows ( $resultSet ) {
        return $this->driver->getSelectedRows ( $resultSet );
    }

    public function setIndex ( $resultSet, $row_num ) {
        return $this->driver->setIndex ( $resultSet, $row_num );
    }

    public function getIndexInfo ( $tableName ) {
        return $this->driver->getIndexInfo($tableName);
    }
}

