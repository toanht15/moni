<?php
/**
 * @package AAFramework
 * @subpackage DB
 * @author Akira Takahashi
 */

/*
 * データベースドライバのインターフェース
 */
interface DriverInterface{
		

	public function connect();
	
	public function execute($sql, $params = NULL);
	
	public function fetch($resultSet);
	
	public function begin();
	
	public function commit();
	
	public function rollback();
	
	public function getTableInfo($table_name);
	
	public function escape($value);
	
	public function getAffectedRows ( );
}

?>
