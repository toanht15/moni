<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>楽天ID楽天ID取引開始　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class RakutenIdStartInput extends BaseInput {

	/**
	 * @var string 取引ID
	 */
	var $accessID;
	/**
	 * @var string トークン
	 */
	var $token;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function RakutenIdStartInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function __construct($params = null) {
		parent::__construct($params);
	}

	
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->accessID;
	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->token;
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->accessID = $accessID;
	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->token = $token;
	}


	/**
	 * デフォルト値設定
	 */
	function setDefaultValues() {
	   
	}

	/**
	 * 入力パラメータ群の値を設定する
	 *
	 * @param IgnoreCaseMap $params 入力パラメータ
	 */
	function setInputValues($params) {
		// 入力パラメータがnullの場合は設定処理を行わない
	    if (is_null($params)) {
	        return;
	    }
	    
		$this->setAccessID($this->getStringValue($params, 'AccessID', $this->getAccessID()));
		$this->setToken($this->getStringValue($params, 'Token', $this->getToken()));

	}

	/**
	 * 文字列表現
	 * @return string 接続文字列表現
	 */
	function toString() {
		$str ='';
		$str .= 'AccessID=' . $this->encodeStr($this->getAccessID());
		$str .='&';
		$str .= 'Token=' . $this->encodeStr($this->getToken());

	    return $str;
	}


}
?>
