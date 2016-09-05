<?php
interface DBInterface {
  public function getDriverName ();
	public function connect ();
	public function execute ( $sql, $params = NULL);
	public function fetch ( $resultSet );
	public function begin ( $isola_level = -1 );
	public function commit ();
	public function rollback ();
	public function getTableInfo ( $table_name );
	public function escape ( $value );
	public function getAffectedRows ();
}

