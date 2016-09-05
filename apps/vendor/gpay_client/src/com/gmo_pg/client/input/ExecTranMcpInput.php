<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>多通貨クレジットカード決済実行　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranMcpInput extends BaseInput {

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
	 * @var string カード番号
	 */
	var $cardNo;
	/**
	 * @var string 有効期限
	 */
	var $expire;
	/**
	 * @var string セキュリティコード
	 */
	var $securityCode;
	/**
	 * @var string 決済結果戻しURL
	 */
	var $retURL;
	/**
	 * @var string 処理NG時URL
	 */
	var $errorRcvURL;
	/**
	 * @var string 加盟店自由項目1
	 */
	var $clientField1;
	/**
	 * @var string 加盟店自由項目2
	 */
	var $clientField2;
	/**
	 * @var string 加盟店自由項目3
	 */
	var $clientField3;
	/**
	 * @var string 加盟店自由項目返却フラグ
	 */
	var $clientFieldFlag;
	/**
	 * @var integer 支払開始期限秒
	 */
	var $paymentTermSec;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function ExecTranMcpInput($params = null) {
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
	 * カード番号取得
	 * @return string カード番号
	 */
	function getCardNo() {
		return $this->cardNo;
	}
	/**
	 * 有効期限取得
	 * @return string 有効期限
	 */
	function getExpire() {
		return $this->expire;
	}
	/**
	 * セキュリティコード取得
	 * @return string セキュリティコード
	 */
	function getSecurityCode() {
		return $this->securityCode;
	}
	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->retURL;
	}
	/**
	 * 処理NG時URL取得
	 * @return string 処理NG時URL
	 */
	function getErrorRcvURL() {
		return $this->errorRcvURL;
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->clientField1;
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->clientField2;
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->clientField3;
	}
	/**
	 * 加盟店自由項目返却フラグ取得
	 * @return string 加盟店自由項目返却フラグ
	 */
	function getClientFieldFlag() {
		return $this->clientFieldFlag;
	}
	/**
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->paymentTermSec;
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
	 * カード番号設定
	 *
	 * @param string $cardNo
	 */
	function setCardNo($cardNo) {
		$this->cardNo = $cardNo;
	}
	/**
	 * 有効期限設定
	 *
	 * @param string $expire
	 */
	function setExpire($expire) {
		$this->expire = $expire;
	}
	/**
	 * セキュリティコード設定
	 *
	 * @param string $securityCode
	 */
	function setSecurityCode($securityCode) {
		$this->securityCode = $securityCode;
	}
	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->retURL = $retURL;
	}
	/**
	 * 処理NG時URL設定
	 *
	 * @param string $errorRcvURL
	 */
	function setErrorRcvURL($errorRcvURL) {
		$this->errorRcvURL = $errorRcvURL;
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->clientField1 = $clientField1;
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->clientField2 = $clientField2;
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->clientField3 = $clientField3;
	}
	/**
	 * 加盟店自由項目返却フラグ設定
	 *
	 * @param string $clientFieldFlag
	 */
	function setClientFieldFlag($clientFieldFlag) {
		$this->clientFieldFlag = $clientFieldFlag;
	}
	/**
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->paymentTermSec = $paymentTermSec;
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
		$this->setCardNo($this->getStringValue($params, 'CardNo', $this->getCardNo()));
		$this->setExpire($this->getStringValue($params, 'Expire', $this->getExpire()));
		$this->setSecurityCode($this->getStringValue($params, 'SecurityCode', $this->getSecurityCode()));
		$this->setRetURL($this->getStringValue($params, 'RetURL', $this->getRetURL()));
		$this->setErrorRcvURL($this->getStringValue($params, 'ErrorRcvURL', $this->getErrorRcvURL()));
		$this->setClientField1($this->getStringValue($params, 'ClientField1', $this->getClientField1()));
		$this->setClientField2($this->getStringValue($params, 'ClientField2', $this->getClientField2()));
		$this->setClientField3($this->getStringValue($params, 'ClientField3', $this->getClientField3()));
		$this->setClientFieldFlag($this->getStringValue($params, 'ClientFieldFlag', $this->getClientFieldFlag()));
		$this->setPaymentTermSec($this->getStringValue($params, 'PaymentTermSec', $this->getPaymentTermSec()));

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
		$str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
		$str .='&';
		$str .= 'Expire=' . $this->encodeStr($this->getExpire());
		$str .='&';
		$str .= 'SecurityCode=' . $this->encodeStr($this->getSecurityCode());
		$str .='&';
		$str .= 'RetURL=' . $this->encodeStr($this->getRetURL());
		$str .='&';
		$str .= 'ErrorRcvURL=' . $this->encodeStr($this->getErrorRcvURL());
		$str .='&';
		$str .= 'ClientField1=' . $this->encodeStr($this->getClientField1());
		$str .='&';
		$str .= 'ClientField2=' . $this->encodeStr($this->getClientField2());
		$str .='&';
		$str .= 'ClientField3=' . $this->encodeStr($this->getClientField3());
		$str .='&';
		$str .= 'ClientFieldFlag=' . $this->encodeStr($this->getClientFieldFlag());
		$str .='&';
		$str .= 'PaymentTermSec=' . $this->encodeStr($this->getPaymentTermSec());

	    return $str;
	}


}
?>
