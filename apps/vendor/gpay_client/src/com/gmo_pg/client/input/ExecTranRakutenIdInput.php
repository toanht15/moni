<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>楽天ID決済実行　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranRakutenIdInput extends BaseInput {

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
	 * @var string 商品ID
	 */
	var $itemId;
	/**
	 * @var string サブ商品ID
	 */
	var $itemSubId;
	/**
	 * @var string 商品名
	 */
	var $itemName;
	/**
	 * @var string 決済結果戻しURL
	 */
	var $retURL;
	/**
	 * @var string 処理NG時URL
	 */
	var $errorRcvURL;
	/**
	 * @var integer 支払開始期限秒
	 */
	var $paymentTermSec;
	/**
	 * @var string 複数商品
	 */
	var $multiItem;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function ExecTranRakutenIdInput($params = null) {
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
	 * 商品ID取得
	 * @return string 商品ID
	 */
	function getItemId() {
		return $this->itemId;
	}
	/**
	 * サブ商品ID取得
	 * @return string サブ商品ID
	 */
	function getItemSubId() {
		return $this->itemSubId;
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getItemName() {
		return $this->itemName;
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
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->paymentTermSec;
	}
	/**
	 * 複数商品取得
	 * @return string 複数商品
	 */
	function getMultiItem() {
		return $this->multiItem;
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
	 * 商品ID設定
	 *
	 * @param string $itemId
	 */
	function setItemId($itemId) {
		$this->itemId = $itemId;
	}
	/**
	 * サブ商品ID設定
	 *
	 * @param string $itemSubId
	 */
	function setItemSubId($itemSubId) {
		$this->itemSubId = $itemSubId;
	}
	/**
	 * 商品名設定
	 *
	 * @param string $itemName
	 */
	function setItemName($itemName) {
		$this->itemName = $itemName;
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
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->paymentTermSec = $paymentTermSec;
	}
	/**
	 * 複数商品設定
	 *
	 * @param string $multiItem
	 */
	function setMultiItem($multiItem) {
		$this->multiItem = $multiItem;
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
		$this->setClientField1($this->getStringValue($params, 'ClientField1', $this->getClientField1()));
		$this->setClientField2($this->getStringValue($params, 'ClientField2', $this->getClientField2()));
		$this->setClientField3($this->getStringValue($params, 'ClientField3', $this->getClientField3()));
		$this->setItemId($this->getStringValue($params, 'ItemId', $this->getItemId()));
		$this->setItemSubId($this->getStringValue($params, 'ItemSubId', $this->getItemSubId()));
		$this->setItemName($this->getStringValue($params, 'ItemName', $this->getItemName()));
		$this->setRetURL($this->getStringValue($params, 'RetURL', $this->getRetURL()));
		$this->setErrorRcvURL($this->getStringValue($params, 'ErrorRcvURL', $this->getErrorRcvURL()));
		$this->setPaymentTermSec($this->getStringValue($params, 'PaymentTermSec', $this->getPaymentTermSec()));
		$this->setMultiItem($this->getStringValue($params, 'MultiItem', $this->getMultiItem()));

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
		$str .= 'ClientField1=' . $this->encodeStr($this->getClientField1());
		$str .='&';
		$str .= 'ClientField2=' . $this->encodeStr($this->getClientField2());
		$str .='&';
		$str .= 'ClientField3=' . $this->encodeStr($this->getClientField3());
		$str .='&';
		$str .= 'ItemId=' . $this->encodeStr($this->getItemId());
		$str .='&';
		$str .= 'ItemSubId=' . $this->encodeStr($this->getItemSubId());
		$str .='&';
		$str .= 'ItemName=' . $this->encodeStr($this->getItemName());
		$str .='&';
		$str .= 'RetURL=' . $this->encodeStr($this->getRetURL());
		$str .='&';
		$str .= 'ErrorRcvURL=' . $this->encodeStr($this->getErrorRcvURL());
		$str .='&';
		$str .= 'PaymentTermSec=' . $this->encodeStr($this->getPaymentTermSec());
		$str .='&';
		$str .= 'MultiItem=' . $this->encodeStr($this->getMultiItem());

	    return $str;
	}


}
?>
