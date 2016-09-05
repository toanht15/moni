<?php
/**
 * @package AAFramework
 * @subpackage aafwPhysicalEntityBase
 * @author Akira Takahashi
 */

AAFW::import ( 'jp.aainc.aafw.db.DB' );

//require_once 'db/DB.php';
//require_once 'aafwConfig.php';

class aafwPhysicalEntityBase{
	protected static $_InTransaction = array();

	const KEY_TYPE_AUTOINCREMENT = 0;
	const KEY_TYPE_UNAUTOSET = 1;

	const DELETE_TYPE_LOGICAL = 0;
	const DELETE_TYPE_PHYSICAL = 1;

	const SELECT_ALL = '#ALL';

	//サブクラスで設定する項目
	protected $table_name; //テーブル名(省略時、クラス名)
	protected $key_list; //主キーのリストを配列で設定(省略時、DBから自動取得)
	protected $key_type; //主キーがautoincrementかどうかを設定(KEY_TYPE_AUTOINCREMENT or KEY_TYPE_UNAUTOSET)(省略時、KEY_TYPE_AUTOINCREMENT)
	protected $key_length; //主キーの文字列長。KEY_TYPE_UNAUTOSETに設定している場合、キー文字列の自動生成に使用する(省略時、DBから取得した文字列長を使用)
	protected $delete_type; //deleteメソッドの削除方式。(DELETE_TYPE_LOGICAL or DELETE_TYPE_PHYSICAL)(省略時、DELETE_TYPE_LOGICAL)
	protected $db_group_id; //テーブルが所属するDBグループID(省略時、$database_info_listの最初のDBグループが設定される)
	protected $db_id; //テーブルが所属するDBID(省略可)
	private static $whereCache = array();
	private static $ConvertNameCache = array();
	/* 関連オブジェクトを配列で指定
	 *
	 * class member extends aafwPhysicalEntityBase{
	 *     $related_entity = array('member_profile', 'member_diary', array('class' => 'member_friend', 'key' => array('key1_related_obj' => 'key1_base_obj', 'key2_related_obj' => 'key2_base_obj') );
	 * }
	 *  ↓↓↓↓
	 *
	 * //1:1
	 * //SELECT * FROM member_profile WHERE member_id = member->id;
	 * $obj->member_profile;
	 *
	 * //1:N
	 * //SELECT * FROM member_diary WHERE member_id = member->id;
	 * foreach($obj->member_diary_list as $member_diary){
	 *     echo $member_diary->title;
	 * }
	 *
	 * //1:N
	 * //SELECT * FROM member_friend WHERE key1_related_obj = member->key1_base_obj AND  key2_related_obj = member->key2_base_obj;
	 * foreach($obj->member_friend_list as $member_friend){
	 *     echo $member_friend->id;
	 * }
	 *
	 */
	protected $related_entity = array();

	protected $column_delete_flg = 'del_flg'; //論理削除のカラム名
	protected $column_create_date = 'date_created'; //作成日時のカラム名
	protected $column_update_date = 'date_updated'; //更新日時のカラム名
	public $db_list = array();
	public static $table_info_list = array();
	public $table_info;
	public $values = array();
	public $old_values = array();

	public function __construct($db_group_id = null, $db_id = null){
		if ($db_group_id) $this->db_group_id = $db_group_id;
		if ($db_id) $this->db_id = $db_id;
		if ($this->db_group_id != '' and  $this->db_id != '') {
			//$db_group_idと$db_idが指定されている場合、そのDBオブジェクトを取得する
			$this->db_list[$this->db_id] = DB::getInstance($this->db_group_id, $this->db_id);
		} else {
			//指定されてなければ属するDBグループ（指定されてなければデフォルト）のDBオブジェクトを全て取得する
			if ($this->db_group_id == '') $this->db_group_id = DB::getDefaultDBGroup();
			$ret = DB::getInstance($this->db_group_id);
			foreach ($ret as $db) {
				$this->db_list[$db->db_id] = $db;
			}
		}

		//テーブル名がからの場合はクラス名をセット
		if (!isset($this->table_name)) $this->table_name = $this->convertName(get_class($this));

		//テーブル情報を取得する
		if (isset(self::$table_info_list[get_class($this)])) {
			$this->table_info = self::$table_info_list[get_class($this)];
		} else {
			self::$table_info_list[get_class($this)] = $this->db_read->getTableInfo($this->table_name);
			$this->table_info = self::$table_info_list[get_class($this)];
		}

		//主キーリスト
		if (!isset($this->key_list)) {
			foreach ($this->table_info as $column => $column_info) {

				if ($column_info[key]) $this->key_list[] = $column;

			}
		}

	}

	//getter $obj->columnでアクセス
	public function __get($column){
		if ($column == 'db_read' && self::$_InTransaction) $column = 'db_write';
		$buf = $this->convertName($column);
		if (!is_null($this->values[$buf])) return $this->values[$buf];
		$this->get_db($column);
		$this->get_related_entity($column);
		return $this->values[$column];
	}

	public function __call($name, $args){
		list($order, $pager) = $args;
		$this->get_db($name);
		$this->get_related_entity($name, $order, $pager);
		return $this->values[$name];
	}

	private function get_db($column){

		if ($column == 'db_read' and !isset($this->values[$column])) {

			if ($this->db_id != '') {
				$this->values[$column] = $this->db_list[$this->db_id];
			} else {
				//read
				$this->values[$column] = $this->db_list['r'];
			}

		}

		if ($column == 'db_write' and !isset($this->values[$column])) {

			if ($this->db_id != '') {
				$this->values[$column] = $this->db_list[$this->db_id];
			} else {
				//read
				$this->values[$column] = $this->db_list['w'];
			}

		}
	}

	private function get_related_entity($column, $order = array(), $pager = array()){
		foreach ($this->related_entity as $temp) {
			if (is_array($temp)) {
				//キー指定
				$class_name = $temp['class'];
				$key_array = $temp['key'];

				//1:1
				if ($this->convertName($column) == $class_name and (!isset($this->values[$column]) || ($order || $page))) {
					//require_once "$class_name.php";

					//SELECT * FROM class_name WHERE %key1% = %value1% AND %key2% = %value2% ... ;
					$RelatedObject = new $class_name();
					$conditions = array();
					foreach ($key_array as $column_related_obj => $column_base_obj) {
						$conditions[$column_related_obj] = $this->values[$column_base_obj];
					}
					$temp_list = $RelatedObject->find($conditions);
					if (count($temp_list) > 0) {
						//findした結果をセット
						$this->values[$column] = $temp_list[0];
					} else {
						//存在しない場合、newしてセット
						$this->values[$column] = new $class_name();
						$this->values[$column]->setValues($conditions);
					}
				}
				//1:n
				if ($column == $class_name . '_list' and (!isset($this->values[$column]) || ($order || $page))) {
					//require_once "$class_name.php";
					$RelatedObject = new $class_name();
					$conditions = array();
					foreach ($key_array as $column_related_obj => $column_base_obj) {
						$conditions[$column_related_obj] = $this->values[$column_base_obj];
					}

					$this->values[$column] = $RelatedObject->find(array(
						'conditions' => $conditions,
						'order' => $order['name'] . ' ' . $order['direction'],
						'limit' => $pager['count'],
						'offset' => ($pager['page'] - 1) * $pager['count'],
					));
				}
			} else {
				$class_name = $temp;
				//1:1
				if ($this->convertName($column) == $class_name and (!isset($this->values[$column]) || ($order || $page))) {
					//require_once "$class_name.php";

					//SELECT * FROM class_name WHERE %this_table_name%_id = $this->id;
					$RelatedObject = new $class_name();
					$temp_list = $RelatedObject->find(array(preg_replace('#s$#', '', $this->table_name) . '_id' => $this->id));
					if (count($temp_list) > 0) {
						//findした結果をセット
						$this->values[$column] = $temp_list[0];
					} else {
						//存在しない場合、newしてセット
						$this->values[$column] = new $class_name();
						$this->values[$column]->values[preg_replace('#s$#', '', $this->table_name) . '_id'] = $this->id;
					}
				}
				//1:n
				if ($this->convertName($column) == $class_name . '_list' and (!isset($this->values[$column]) || ($order || $page))) {

					//require_once "$class_name.php";
					$RelatedObject = new $class_name();
					$this->values[$column] = $RelatedObject->find(array(
						'conditions' => array(preg_replace('#s$#', '', $this->table_name) . '_id' => $this->id),
						'order' => $order['name'] . ' ' . $order['direction'],
						'limit' => $pager['count'],
						'offset' => ($pager['page'] - 1) * $pager['count'],
					));
				}

			}
		}
	}

	//setter $obj->columnでアクセス
	public function __set($column, $value){

		$this->values[$column] = $value;
	}

	//配列$params(column => value)から$valuesにデータをセットする
	public function setValues($params){

		foreach ($params as $column => $value) {
			$this->values[$column] = $value;
		}

	}

	//配列$params(column => value)から$old_valuesにデータをセットする
	public function setOldValues($params){
		foreach ($params as $column => $value) {
			$this->old_values[$column] = $value;
		}
	}

	public function getValues(){
		return $this->values;
	}

	private function setKeyValue(){

		//存在確認10回までtry
		for ($i = 0; $i < 10; $i++) {

			//キーをセット
			foreach ($this->key_list as $column) {

				if (isset($this->key_length) and $this->key_length <= $this->table_info[$column][length]) {
					//カラムの桁数を超えない場合は指定されたキーの長さを使用
					$key_length = $this->key_length;
				} elseif ($this->table_info[$column][length] != '') {
					//それ以外はカラム長を使用
					$key_length = $this->table_info[$column][length];
				} else {
					//カラム長がない場合は固定
					$key_length = 16;
				}


				$this->values[$column] = $this->getRandomString($key_length, $this->table_info[$column][type]);

			}

			//存在確認してOKならbreak
			if (!$this->isSaved()) break;

		}

	}

	private function getRandomString($length = 16, $type = 'string'){

		$random_char = 'abcdefghijklmnopqrstuvwxyz0123456789';

		//文字列タイプの場合
		if ($type == 'string') {

			mt_srand();
			for ($i = 0; $i < $length; $i++) {
				$random_string .= $random_char{mt_rand(0, strlen($random_char) - 1)};
			}

		} else {

			//数値タイプ
			mt_srand();
			$random_string = mt_rand(int('1' . str_repeat('0', $length - 1)), int('1' . str_repeat('9', $length)));

		}

		return $random_string;

	}


	public function create(){
		//主キーの設定
		//autoincrementでない場合はキーの設定をする
		$key_set_flg = false;
		if ($this->key_type == self::KEY_TYPE_UNAUTOSET) {

			$key_set_flg = true;
			foreach ($this->key_list as $column) {

				if ($this->values[$column] != '') {
					//キーに値が設定されている場合はセットしない
					$key_set_flg = false;
					break;
				}

			}
		}
		if ($key_set_flg) $this->setKeyValue();
		$tempValues = $this->values;
		foreach ($this->table_info as $column => $column_info) {
			if ($column == $this->column_create_date or  $column == $this->column_update_date) {
				$column_value = $this->values[$column];
				//作成日付 or 更新日付 テストデータに明示的に値が指定されている場合はその値を使う
				if ($column_value == '' || $column_value == null) {
					$tempValues[$column] = 'NOW()';
				} else {
					$column_value = str_replace('-', '', str_replace(':', '', str_replace(' ', '', $column_value)));
					$tempValues[$column] = "'$column_value'";
				}
			} elseif ($column == $this->column_delete_flg) {
				//削除フラグ
				$tempValues[$column] = "'0'";
			} elseif (strstr($tempValues[$column], '@FUNC@')) {
				//関数
				$tempValues[$column] = str_replace('@FUNC@', '', $tempValues[$column]);
			} elseif (isset($tempValues[$column])) {
				//値がセットされている場合
				$tempValues[$column] = "'" . $this->db_write->escape($tempValues[$column]) . "'";
			}

			//値がセットされている場合のみSQL作成
			if (isset($tempValues[$column])) {
				if ($sql_columns == '') {
					$sql_columns = $column;
				} else {
					$sql_columns .= ", $column";
				}

				if ($sql_values == '') {
					$sql_values = $tempValues[$column];
				} else {
					$sql_values .= ", " . $tempValues[$column];
				}
			}
		}

		$sql = "INSERT INTO $this->table_name ( $sql_columns ) VALUES($sql_values); ";
		$this->db_write->execute($sql);
		if (!$this->id) {
			$rs = $this->db_write->execute('select last_insert_id() as id;');
			$row = $this->db_write->fetch($rs);
			$this->id = $row['id'];
		}
	}

	public function update(){

		$tempValues = $this->values;

		foreach ($this->table_info as $column => $column_info) {

			if ($column == $this->column_update_date) {

				//更新日付
				$tempValues[$column] = 'NOW()';

			} elseif (strstr($tempValues[$column], '@FUNC@')) {

				//関数
				$tempValues[$column] = str_replace('@FUNC@', '', $tempValues[$column]);
				// 明示的にNULLを突っ込む場合
			} elseif (strstr($tempValues[$column], "\x0bNULL\x0b")) {

				$tempValues[$column] = 'null';

			} elseif (isset($tempValues[$column])) {

				//値がセットされている場合
				$tempValues[$column] = "'" . $this->db_write->escape($tempValues[$column]) . "'";

			}

			//値がセットされている場合のみSQL作成
			if (isset($tempValues[$column])) {

				if ($sql_set == '') {
					$sql_set = "$column = " . $tempValues[$column];
				} else {
					$sql_set .= ", $column = " . $tempValues[$column];
				}

			}

		}


		$sql = "UPDATE $this->table_name SET $sql_set " . $this->createThisWhere();
		$this->db_write->execute($sql);


	}

	private function createThisWhere($new_old = 'old'){

		//Where句
		if ($new_old == 'old' and count($this->old_values) > 0) {
			//キーをold_valuesから作成
			$tempValues = $this->old_values;
		} else {
			//キーをvaluesから作成
			$tempValues = $this->values;
		}

		foreach ($this->key_list as $key_column) {
			if ($sql_where == '') {
				$sql_where = "WHERE $key_column = '" . $this->db_write->escape($tempValues[$key_column]) . "' ";
			} else {
				$sql_where .= "AND $key_column = '" . $this->db_write->escape($tempValues[$key_column]) . "' ";
			}

		}
		return $sql_where;

	}

	public function save(){

		if ($this->isSaved()) {
			//保存されている場合はupdate
			$this->update();

		} else {
			$this->create();

		}

	}

	public function deleteLogical(){

		$sql = "UPDATE $this->table_name SET $this->column_delete_flg = '1', $this->column_update_date = NOW() " . $this->createThisWhere();
		$this->db_write->execute($sql);

	}

	public function deletePhysical(){

		$sql = "DELETE FROM $this->table_name  " . $this->createThisWhere();
		$this->db_write->execute($sql);

	}

	public function delete(){

		if (!isset($this->delete_type) or $this->delete_type == self::DELETE_TYPE_LOGICAL) {
			$this->deleteLogical();
		} else {
			$this->deletePhysical();
		}

	}

	public function reload($new_old = 'new', $rw = 'w'){

		$sql = "SELECT * FROM " . $this->table_name . ' ' . $this->createThisWhere($new_old);
		if ($rw == 'r') {
			$row = $this->db_read->fetch($this->db_read->execute($sql));
		} else {
			$row = $this->db_write->fetch($this->db_read->execute($sql));
		}

		$this->setValues($row);
		$this->setOldValues($row);

	}

	public function isSaved(){

		$conditions = array();
		foreach ($this->key_list as $column) {
			$conditions[$column] = $this->values[$column];
		}
		if ($conditions) $ret = $this->count($conditions);

		if ($ret > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function exists(){
		return $this->isSaved();
	}

	public function isChanged($column = NULL){
		if ($this->values == $this->old_values) {
			return false;
		} else {
			return true;
		}
	}

	private function createConditionWhere($conditions = NULL, $where = NULL, $where_params = NULL){
//    if ( self::$whereCache[$this->table_name][serialize( $conditions )][$where][serialize( $where_params)] ) return self::$whereCache[$this->table_name][serialize( $conditions )][$where][serialize( $where_params)];
		$sql_where = '';
		if (isset($conditions)) {

			//$conditions => array(key1[:operator1] => value1, key2[:operator2] => value2)
			//(operatorを省略するとデフォルトは"=")
			//配列の場合、key1 operator1 value1 AND key2 operator2 value2
			//(IS NULL or IS NOT NULL はオペランドなし)
			if (is_array($conditions)) {

				foreach ($conditions as $key => $value) {
					preg_match('/([^:]+):?([^:]*)/', $key, $matches);
					$column = $matches[1];
					$operator = $matches[2];
					if ($operator == '') {
						if (is_array($value)) {
							$operator = 'in';
						} else {
							$operator = '=';
						}
					}

					if (preg_match('/IS\s+NULL|IS\s+NOT\s+NULL/i', $operator)) {
						//IS NULL or IS NOT NULL はオペランドなし
						$operand = '';
					} else {
						if (is_array($value)) {
							$buf = array();
							$null_flg = 0;
							foreach ($value as $key) {
								if (strtoupper($key) == 'NULL') $null_flg = 1;
								else                               $buf[] = "'" . $this->db_read->escape($key) . "'";
							}
							if ($buf) {
								$operand = '(' . join(',', $buf) . ') ';
								if ($null_flg) {
									$operand .= ' or ' . $column . ' is null ) ';
									$column = '(' . $column;
								}
							} else {
								$operand = '1';
								$operator = '=';
								$column = '1';
							}
						} else {
							if (is_null($value)) {
								//$valueにnullが渡ってきたら条件をなくすのではなく IS NULL にする
								$operator = 'IS NULL';
								$operand = '';
							} else {
								$operand = "'" . $this->db_read->escape($value) . "'";
							}
						}
					}
					if ($sql_where == '') {
						$sql_where = "WHERE $column $operator $operand ";
					} else {
						$sql_where .= " AND $column $operator $operand ";
					}
				}
			} elseif ($conditions === self::SELECT_ALL) {
				//全件検索の為処理なし
			} else {
				//$conditions => value
				//配列でない場合で$key_listが単数の場合、key=value、$key_listが単数出ない場合はエラー
				if (count($this->key_list) == 1) {
					$sql_where = "WHERE " . $this->key_list[0] . " = '" . $this->db_read->escape($conditions) . "'";
				} else {
					//エラー
					throw new Exception('Key_list is not single and conditions is not array');
				}
			}
		} else {
			//$conditions => value
			//conditionsがnullで$key_listが単数の場合、key=value、$key_listが単数出ない場合はエラー
			if (count($this->key_list) == 1) {
				$sql_where = "WHERE " . $this->key_list[0] . " IS NULL";
			} else {
				//エラー
				throw new Exception('Key_list is not single and conditions is null');
			}
		}

		if (((is_array($conditions) && !$conditions[$this->column_delete_flg]) || !is_array($conditions)) &&
			$this->column_delete_flg != '' and
			isset($this->table_info[$this->column_delete_flg])
		) {
			if ($sql_where == '') {
				$sql_where = "WHERE $this->column_delete_flg = '0' ";
			} else {
				$sql_where .= " AND $this->column_delete_flg = '0' ";
			}
		}

		if ($where != '') {
			if (is_array($where_params)) {
				foreach ($where_params as $key => $value) {
					if (is_array($value)) {
						$vals = array();
						foreach ($value as $val) $vals[] = "'" . $this->db_read->escape($val) . "'";
						$where = str_replace($key, join(',', $vals), $where);
					} else {
						$where = str_replace($key, $this->db_read->escape($value), $where);
					}
				}
			}
			if ($sql_where == '') {
				$sql_where = "WHERE $where ";
			} else {
				$sql_where .= " AND $where ";
			}
		}
//    self::$whereCache[$this->table_name][serialize( $conditions )][$where][serialize( $where_params)] =$sql_where;
		return $sql_where;
	}

	/*
	 * 例
	 *
	 * PKを指定して検索　戻り値はオブジェクト
	 * //SELECT * FROM member WHERE id = '0'
	 * $member = $member->find(0);
	 *
	 * 検索条件を指定して検索　戻り値はオブジェクトを格納した配列
	 * //SELECT * FROM member WHERE id = '0'
	 * $member_array = $member->find(array('id' => '0'));
	 *
	 * オペレータは":"で指定
	 * //SELECT * FROM member WHERE id <= '10' AND name != 'takahashi'
	 * $member_array = $member->find(array('id:<=' => '10', 'name:!=' => 'takahashi'));
	 *
	 * ORDER BY、LIMITなどを指定
	 * //SELECT * FROM member WHERE id <= '10' ORDER BY date_created DESC LIMIT 5, 3
	 * $member_array = $member->find(array('conditions' => array('id:<=' => '10'), 'order' => 'date_created DESC', 'limit' => 3, 'offset' => 5));
	 *
	 * WHERE句を追加
	 * //SELECT * FROM member WHERE id <= '10' AND member_id BETWEEN 4 AND 7
	 * $member_array = $member->find(array('conditions' => array('id:<=' => '10'), 'where' => "member_id BETWEEN '%min%' AND '%max%'", 'where_params' => array('%min%' => 4, '%max%' => 7)));
	 *
	 *
	 */
	public function find($find_arg = NULL, $for_update = false, $no_fetch = false){

		// 引数がなければ全件検索
		$args = func_num_args();
		if ($args === 0) $find_arg = self::SELECT_ALL;

		//配列でない場合は$find_arg⇒キー
		if (!is_array($find_arg)) {

			$conditions = $find_arg;

		} elseif (array_key_exists('conditions', $find_arg) or array_key_exists('order', $find_arg) or
			array_key_exists('limit', $find_arg) or array_key_exists('offset', $find_arg) or array_key_exists('where', $find_arg)
		) {
			//配列でconditions、order…のいずれかがキーとして存在している場合
			//それぞれを配列から取り出す
			if( !$find_arg['conditions'] ){
				// キーにconditionsがない場合は全件検索
				$conditions = self::SELECT_ALL;
			}else{
				$conditions = $find_arg['conditions'];
			}
			$order = $find_arg['order'];
			$limit = $find_arg['limit'];
			$offset = $find_arg['offset'];
			$where = $find_arg['where'];
			$where_params = $find_arg['where_params'];
			$pager = $find_arg['pager'];

		} else {
			//配列でキーとしてconditions、order、limit、offset、whereが存在しない場合
			//$find_arg⇒conditions

			$conditions = $find_arg;

		}


		$sql = "SELECT * FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		if (is_array($order)) {
			if ($order['name']) $order = array($order);
			$buf = array();
			foreach ($order as $row) {
				if ($row['name']) $buf[] = $row['name'] . ' ' . ($row['direction'] ? $row['direction'] : 'asc');
			}
			$sql .= 'order by ' . join(',', $buf) . ' ';
		} else {
			if (trim($order) != '') {
				$sql .= 'ORDER BY ' . $order . ' ';
			}
		}
		if ($pager) {
			if (!$pager['page']) $pager['page'] = '1';
			$sql .= ' limit ' . ($pager['count'] * ($pager['page'] - 1)) . ',' . $pager['count'];
		} else {
			if (isset($offset) and isset($limit)) {
				$sql .= " LIMIT $offset, $limit";
			} elseif (isset($limit)) {
				$sql .= " LIMIT $limit";
			}
		}

		if ($for_update) $sql .= ' FOR UPDATE';
		$rs = $this->db_read->execute($sql);
		if ($no_fetch) return $rs;
		$ret = array();
		$class_name = get_class($this);
		while ($row = $this->db_read->fetch($rs)) {
			$tempObj = new $class_name();
			$tempObj->setValues($row);
			$tempObj->setOldValues($row);
			$ret[] = $tempObj;
		}
		if ($ret) {
			$this->setValues($ret[0]->getValues());
			$this->setOldValues($ret[0]->getValues());
		}
		if (!is_array($conditions) and isset($conditions) and count($this->key_list) == 1 and count($ret) != 0 and $conditions != self::SELECT_ALL) {
			return $this;
		} else {
			return $ret;
		}
	}

	public function fetch($rs){
		$class_name = get_class($this);
		$tempObj = null;
		if ($row = $this->db_read->fetch($rs)) {
			$tempObj = new $class_name();
			$tempObj->setValues($row);
			$tempObj->setOldValues($row);
		}
		return $tempObj;
	}

	public function getResultRowCount($rs){
		return $this->db_read->getResultRowCount($rs);
	}

	/**
	 * "FOR UPDATE"をつけてfindする
	 * @param findと同じ
	 * @return findと同じ
	 */
	public function findForUpdate($find_arg = NULL){
		if (self::$_InTransaction) return $this->find($find_arg, true);
		else                        return $this->find($find_arg);
	}

	/**
	 * 書き込み用テーブルに対してfindメソッドを実行する
	 * @param findと同じ
	 * @return findと同じ
	 **/
	public function findFromWriteDB($find_arg = NULL){
		$read = $this->db_read;
		$this->db_read = $this->db_write;
		$result = $this->find($find_arg);
		$this->db_read = $read;
		return $result;
	}

	public function find_by_sql($sql, $params = array()){

		if (is_array($params)) {
			foreach ($params as $key => $value) {

				$sql = str_replace($key, $this->db_read->escape($value), $sql);

			}
		}

		$rs = $this->db_read->execute($sql);
		$ret = array();
		while ($row = $this->db_read->fetch($rs)) {

			$class_name = get_class($this);
			$tempObj = new $class_name();
			$tempObj->setValues($row);
			$tempObj->setOldValues($row);
			$ret[] = $tempObj;
		}

		return $ret;

	}

	/**
	 * 件数を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function count($conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数がなければ全件検索
		$args = func_num_args();
		if ($args === 0) $conditions = self::SELECT_ALL;

		$col = null;
		$sql = '';
		if (is_array($conditions) && is_string($conditions["__col_name"])) {
			$col = $conditions["__col_name"];
			$sql .= "SELECT COUNT( DISTINCT `" . str_replace('`', '', $conditions['__col_name']) . "`) cnt ";
			unset ($conditions["__col_name"]);
		} else {
			$sql .= "SELECT COUNT(*) cnt ";
		}
		$sql .= " FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row[cnt];
	}

	/**
	 * 平均を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getAverage($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL(AVG(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * 最大値を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getMax($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL(max(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * 合計を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getSum($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL( sum(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * 最小値を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getMin($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL(min(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * 標準偏差を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getSTDDev($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL(STDDEV(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * 分散を取得する
	 * @param 条件
	 * @param where句(省略可)
	 * @param where句のパラメータ
	 * @return 件数
	 **/
	public function getVariance($col_name, $conditions = NULL, $where = NULL, $where_params = NULL){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$sql = "SELECT IFNULL(VARIANCE(`" . $col_name . "`),0) as hoge  FROM " . $this->table_name . ' ' . $this->createConditionWhere($conditions, $where, $where_params);
		$row = $this->db_read->fetch($this->db_read->execute($sql));
		return $row['hoge'];
	}

	/**
	 * id, name のkey=>valueを作成する
	 **/
	public function getDictionary(){
		$sql = "SELECT id ,name FROM " . $this->table_name . ' order by id';
		$rs = $this->db_read->execute($sql);
		$ret = array();
		while ($row = $this->db_read->fetch($rs)) $ret[$row['id']] = $row['name'];
		return $ret;
	}

	/**
	 * らくだ記法を小文字アンスコに変換
	 **/
	private function convertName($str){
		if (self::$ConvertNameCache[$str]) return self::$ConvertNameCache[$str];
		if (preg_match('#[A-Z]#', $str)) {
			$ret = '';
			$count = strlen($str);
			for ($i = 0; $i < $count; $i++) {
				if (!$i) {
					$ret .= strtolower($str[$i]);
				} else {
					if (preg_match('#[A-Z]#', $str[$i])) $ret .= '_';
					$ret .= strtolower($str[$i]);
				}
			}
		} else {
			$ret = $str;
		}
		self::$ConvertNameCache[$str] = $ret;
		return $ret;
	}

	/**
	 * CSVをロードする
	 * @param テーブル作関先のDBグループ
	 **/
	public function createTableTo($db_group){
		$db = DB::getInstance($db_group, 'r');
		$buf = array();
		$db->execute('drop table ' . ' if exists ' . $this->table_name);
		$sql .= 'create table ' . $this->table_name . '(';
		$auto_incre = array();
		foreach ($this->table_info as $row) {
			$type = $row['type_org'] . ($row['length'] ? '(' . $row['length'] . ($row['length_decimally'] ? ',' . $row['length_decimally'] : '') . ')' : '');
			$nullable = $row['nullable'] ? 'null' : 'not null';
			$buf[] = $row['column'] . ' ' . $type . ' ' . $nullable;
			if ($row['extra'] == 'auto_increment') $auto_incre[] = 'alter table ' . $this->table_name . ' change ' . $row['column'] . ' ' . $row['column'] . ' ' . $type . ' ' . $nullable . ' auto_increment';
		}
		$sql .= join(',', $buf) . ');';
		if ($db->getDriverName() == 'mysql') $sql .= 'ENGINE=INNODB;';
		else                                   $sql .= ';';
		$db->execute($sql);
		$db->execute('alter table ' . $this->table_name . ' add primary key (' . join(',', $this->key_list) . ');');
		foreach ($auto_incre as $sql) if ($sql) $db->execute($sql);
	}

	/**
	 * テーブルを削除する
	 * @param drop tableするDBグループ
	 **/
	public function dropTableTo($db_group){
		DB::getInstance($db_group, 'r')->execute('drop table if exists ' . $this->table_name);
	}

	/**
	 * CSVをロードする
	 * @param CSVファイルのパスまたはCSVデータ 一行目はカラム名のヘッダ
	 * @param ロード先のDBグループ
	 **/
	public function loadCSVTo($data, $db_group = null){
		// csv解析
		if (is_file($data)) $data = mb_convert_encoding(file_get_contents($data), 'UTF8', 'sjis,utf8,euc-jp');
		else                    $data = mb_convert_encoding($data, 'UTF8', 'sjis,utf8,euc-jp');
		list($quote, $buf, $header, $rows, $columns) = array('', '', array(), array(), array());
		$data = preg_replace('#\r\n|\r|\n#', "\n", $data);
		for ($i = 0; $i < mb_strlen($data); $i++) {
			$char = $data[$i];
			if ($quote) {
				if ($quote == $char) $quote = '';
				elseif ($char == '\\') {
					$buf .= $char . $data[$i + 1];
					$i++;
				} else                         $buf .= $char;
			} else {
				if (!$buf && $char == ' ') continue;
				elseif ($char == '"' || $char == "'") {
					$quote = $char;
				} elseif ($char == "\n") {
					if ($buf) {
						$columns[] = $buf;
						if (!$header) $header = array_map(create_function('$x', 'return preg_replace( "#:.+$#", "", $x );'), $columns);
						elseif ($columns) $rows[] = $columns;
					}
					$buf = '';
					$columns = array();
				} elseif ($char == ',') {
					$columns[] = $buf;
					$buf = '';
				} else {
					$buf .= $char;
				}
			}
		}
		// 最後が改行じゃない場合の対応
		if ($buf) {
			$columns[] = $buf;
			$rows[] = $columns;
		}

		// データのセーブ
		$class = get_class($this);
		foreach ($rows as $row) {
			$columns = $row;
			$obj = new $class ($db_group);
			for ($i = 0; $i < count($header); $i++) {
				if ($columns[$i] == 'NULL') continue;
				$obj->$header[$i] = $columns[$i];
			}
			$obj->save();
		}
	}

	/**
	 * トランザクションを開始する
	 * @return void
	 **/
	public function begin($isola_level = -1){
		if (!self::$_InTransaction[$this->db_group_id]++) $this->db_write->begin($isola_level);
	}

	/**
	 * トランザクションをコミットする
	 * @return void
	 **/
	public function commit(){
		if (!self::$_InTransaction[$this->db_group_id]) return;
		if (!--self::$_InTransaction[$this->db_group_id]) $this->db_write->commit();
	}

	/**
	 * ロールバックする
	 * @return void
	 **/
	public function rollback(){
		if (!self::$_InTransaction[$this->db_group_id]) return;
		if (!--self::$_InTransaction[$this->db_group_id]) $this->db_write->rollback();
	}

	public static function getCalledObject($db_group_id = null, $db_id = null){
		$subClass = get_called_class();
		if ($subClass == __CLASS__) return false;
		return new $subClass($db_group_id, $db_id);
	}

	public static function finds($find_arg = NULL, $for_update = false, $no_fetch = false, $db_group_id = null, $db_id = null){

		// 引数がなければ全件検索
		$args = func_num_args();
		if ($args === 0) $find_arg = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->find($find_arg, $for_update, $no_fetch);
	}

	public static function counts($conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数がなければ全件検索
		$args = func_num_args();
		if ($args === 0) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->count($conditions, $where, $where_params);
	}

	public static function getAverages($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getAverage($col_name, $conditions, $where, $where_params);
	}

	public static function getMaxs($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getMax($col_name, $conditions, $where, $where_params);
	}

	public static function getSums($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getSum($col_name, $conditions, $where, $where_params);
	}

	public static function getMins($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getMin($col_name, $conditions, $where, $where_params);
	}

	public static function getSTDDevs($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getSTDDev($col_name, $conditions, $where, $where_params);
	}

	public static function getVariances($col_name, $conditions = NULL, $where = NULL, $where_params = NULL, $db_group_id = null, $db_id = null){

		// 引数が1の場合全件検索
		$args = func_num_args();
		if ($args === 1) $conditions = self::SELECT_ALL;

		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getVariance($col_name, $conditions, $where, $where_params);
	}

	public static function fetchs($rs, $db_group_id = null, $db_id = null){
		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->fetch($rs);
	}

	public static function getResultRowCounts($rs, $db_group_id = null, $db_id = null){
		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->getResultRowCount($rs);
	}

	public static function begins($isola_level = null, $db_group_id = null, $db_id = null){
		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->begin($isola_level);
	}

	public static function commits($db_group_id = null, $db_id = null){
		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->commit();
	}

	public static function rollbacks($db_group_id = null, $db_id = null){
		$subClassObj = self::getCalledObject($db_group_id = null, $db_id = null);
		return $subClassObj->rollback();
	}
}
