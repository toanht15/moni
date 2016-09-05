<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>銀行振込(バーチャル口座)決済実行　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranVirtualaccountInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string 取引ID
	 */
	var $accessID;
	/**
	 * @var string 取引パスワード
	 */
	var $accessPass;
	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var integer 取引有効日数
	 */
	var $tradeDays;
	/**
	 * @var string 取引事由
	 */
	var $tradeReason;
	/**
	 * @var string 振込依頼者氏名
	 */
	var $tradeClientName;
	/**
	 * @var string 振込依頼者メールアドレス
	 */
	var $tradeClientMailaddress;
	/**
	 * @var string 加盟店自由項目１
	 */
	var $clientField1;
	/**
	 * @var string 加盟店自由項目２
	 */
	var $clientField2;
	/**
	 * @var string 加盟店自由項目３
	 */
	var $clientField3;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function ExecTranVirtualaccountInput($params = null) {
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
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->shopID;
	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->shopPass;
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->accessID;
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->accessPass;
	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->orderID;
	}
	/**
	 * 取引有効日数取得
	 * @return integer 取引有効日数
	 */
	function getTradeDays() {
		return $this->tradeDays;
	}
	/**
	 * 取引事由取得
	 * @return string 取引事由
	 */
	function getTradeReason() {
		return $this->tradeReason;
	}
	/**
	 * 振込依頼者氏名取得
	 * @return string 振込依頼者氏名
	 */
	function getTradeClientName() {
		return $this->tradeClientName;
	}
	/**
	 * 振込依頼者メールアドレス取得
	 * @return string 振込依頼者メールアドレス
	 */
	function getTradeClientMailaddress() {
		return $this->tradeClientMailaddress;
	}
	/**
	 * 加盟店自由項目１取得
	 * @return string 加盟店自由項目１
	 */
	function getClientField1() {
		return $this->clientField1;
	}
	/**
	 * 加盟店自由項目２取得
	 * @return string 加盟店自由項目２
	 */
	function getClientField2() {
		return $this->clientField2;
	}
	/**
	 * 加盟店自由項目３取得
	 * @return string 加盟店自由項目３
	 */
	function getClientField3() {
		return $this->clientField3;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->shopID = $shopID;
	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->shopPass = $shopPass;
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
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->accessPass = $accessPass;
	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->orderID = $orderID;
	}
	/**
	 * 取引有効日数設定
	 *
	 * @param integer $tradeDays
	 */
	function setTradeDays($tradeDays) {
		$this->tradeDays = $tradeDays;
	}
	/**
	 * 取引事由設定
	 *
	 * @param string $tradeReason
	 */
	function setTradeReason($tradeReason) {
		$this->tradeReason = $tradeReason;
	}
	/**
	 * 振込依頼者氏名設定
	 *
	 * @param string $tradeClientName
	 */
	function setTradeClientName($tradeClientName) {
		$this->tradeClientName = $tradeClientName;
	}
	/**
	 * 振込依頼者メールアドレス設定
	 *
	 * @param string $tradeClientMailaddress
	 */
	function setTradeClientMailaddress($tradeClientMailaddress) {
		$this->tradeClientMailaddress = $tradeClientMailaddress;
	}
	/**
	 * 加盟店自由項目１設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->clientField1 = $clientField1;
	}
	/**
	 * 加盟店自由項目２設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->clientField2 = $clientField2;
	}
	/**
	 * 加盟店自由項目３設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->clientField3 = $clientField3;
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
	    
		$this->setShopID($this->getStringValue($params, 'ShopID', $this->getShopID()));
		$this->setShopPass($this->getStringValue($params, 'ShopPass', $this->getShopPass()));
		$this->setAccessID($this->getStringValue($params, 'AccessID', $this->getAccessID()));
		$this->setAccessPass($this->getStringValue($params, 'AccessPass', $this->getAccessPass()));
		$this->setOrderID($this->getStringValue($params, 'OrderID', $this->getOrderID()));
		$this->setTradeDays($this->getStringValue($params, 'TradeDays', $this->getTradeDays()));
		$this->setTradeReason($this->getStringValue($params, 'TradeReason', $this->getTradeReason()));
		$this->setTradeClientName($this->getStringValue($params, 'TradeClientName', $this->getTradeClientName()));
		$this->setTradeClientMailaddress($this->getStringValue($params, 'TradeClientMailaddress', $this->getTradeClientMailaddress()));
		$this->setClientField1($this->getStringValue($params, 'ClientField1', $this->getClientField1()));
		$this->setClientField2($this->getStringValue($params, 'ClientField2', $this->getClientField2()));
		$this->setClientField3($this->getStringValue($params, 'ClientField3', $this->getClientField3()));

	}

	/**
	 * 文字列表現
	 * @return string 接続文字列表現
	 */
	function toString() {
		$str ='';
		$str .= 'ShopID=' . $this->encodeStr($this->getShopID());
		$str .='&';
		$str .= 'ShopPass=' . $this->encodeStr($this->getShopPass());
		$str .='&';
		$str .= 'AccessID=' . $this->encodeStr($this->getAccessID());
		$str .='&';
		$str .= 'AccessPass=' . $this->encodeStr($this->getAccessPass());
		$str .='&';
		$str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .='&';
		$str .= 'TradeDays=' . $this->encodeStr($this->getTradeDays());
		$str .='&';
		$str .= 'TradeReason=' . $this->encodeStr($this->getTradeReason());
		$str .='&';
		$str .= 'TradeClientName=' . $this->encodeStr($this->getTradeClientName());
		$str .='&';
		$str .= 'TradeClientMailaddress=' . $this->encodeStr($this->getTradeClientMailaddress());
		$str .='&';
		$str .= 'ClientField1=' . $this->encodeStr($this->getClientField1());
		$str .='&';
		$str .= 'ClientField2=' . $this->encodeStr($this->getClientField2());
		$str .='&';
		$str .= 'ClientField3=' . $this->encodeStr($this->getClientField3());

	    return $str;
	}


}
?>
