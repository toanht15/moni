<?php
require_once 'com/gmo_pg/client/input/EntryTranRakutenIdInput.php';
require_once 'com/gmo_pg/client/input/ExecTranRakutenIdInput.php';
/**
 * <b>楽天ID登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRakutenIdInput {

	/**
	 * @var EntryTranRakutenIdInput 楽天ID登録入力パラメータ
	 */
	var $entryTranRakutenIdInput;/* @var $entryTranInput EntryTranRakutenIdInput */

	/**
	 * @var ExecTranRakutenIdInput 楽天ID実行入力パラメータ
	 */
	var $execTranRakutenIdInput;/* @var $execTranInput ExecTranRakutenIdInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranRakutenIdInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranRakutenIdInput = new EntryTranRakutenIdInput($params);
		$this->execTranRakutenIdInput = new ExecTranRakutenIdInput($params);
	}

	/**
	 * 楽天ID取引登録入力パラメータ取得
	 *
	 * @return EntryTranRakutenIdInput 取引登録時パラメータ
	 */
	function &getEntryTranRakutenIdInput() {
		return $this->entryTranRakutenIdInput;
	}

	/**
	 * 楽天ID実行入力パラメータ取得
	 * @return ExecTranRakutenIdInput 決済実行時パラメータ
	 */
	function &getExecTranRakutenIdInput() {
		return $this->execTranRakutenIdInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranRakutenIdInput->getShopID();

	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranRakutenIdInput->getShopPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranRakutenIdInput->getOrderID();

	}
	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->entryTranRakutenIdInput->getJobCd();
	}
	/**
	 * 利用料金取得
	 * @return integer 利用料金
	 */
	function getAmount() {
		return $this->entryTranRakutenIdInput->getAmount();
	}
	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->entryTranRakutenIdInput->getTax();
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranRakutenIdInput->getAccessID();
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranRakutenIdInput->getAccessPass();
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranRakutenIdInput->getClientField1();
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranRakutenIdInput->getClientField2();
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranRakutenIdInput->getClientField3();
	}
	/**
	 * 商品ID取得
	 * @return string 商品ID
	 */
	function getItemId() {
		return $this->execTranRakutenIdInput->getItemId();
	}
	/**
	 * サブ商品ID取得
	 * @return string サブ商品ID
	 */
	function getItemSubId() {
		return $this->execTranRakutenIdInput->getItemSubId();
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getItemName() {
		return $this->execTranRakutenIdInput->getItemName();
	}
	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->execTranRakutenIdInput->getRetURL();
	}
	/**
	 * 処理NG時URL取得
	 * @return string 処理NG時URL
	 */
	function getErrorRcvURL() {
		return $this->execTranRakutenIdInput->getErrorRcvURL();
	}
	/**
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->execTranRakutenIdInput->getPaymentTermSec();
	}
	/**
	 * 複数商品取得
	 * @return string 複数商品
	 */
	function getMultiItem() {
		return $this->execTranRakutenIdInput->getMultiItem();
	}

	/**
	 * 楽天ID取引登録入力パラメータ設定
	 *
	 * @param EntryTranRakutenIdInput entryTranRakutenIdInput  取引登録入力パラメータ
	 */
	function setEntryTranRakutenIdInput(&$entryTranRakutenIdInput) {
		$this->entryTranRakutenIdInput = $entryTranRakutenIdInput;
	}

	/**
	 * 楽天ID実行入力パラメータ設定
	 *
	 * @param ExecTranRakutenIdInput  execTranRakutenIdInput   決済実行入力パラメータ
	 */
	function setExecTranRakutenIdInput(&$execTranRakutenIdInput) {
		$this->execTranRakutenIdInput = $execTranRakutenIdInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranRakutenIdInput->setShopID($shopID);
		$this->execTranRakutenIdInput->setShopID($shopID);

	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranRakutenIdInput->setShopPass($shopPass);
		$this->execTranRakutenIdInput->setShopPass($shopPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranRakutenIdInput->setOrderID($orderID);
		$this->execTranRakutenIdInput->setOrderID($orderID);

	}
	/**
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->entryTranRakutenIdInput->setJobCd($jobCd);
	}
	/**
	 * 利用料金設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->entryTranRakutenIdInput->setAmount($amount);
	}
	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->entryTranRakutenIdInput->setTax($tax);
	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranRakutenIdInput->setAccessID($accessID);
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranRakutenIdInput->setAccessPass($accessPass);
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranRakutenIdInput->setClientField1($clientField1);
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranRakutenIdInput->setClientField2($clientField2);
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranRakutenIdInput->setClientField3($clientField3);
	}
	/**
	 * 商品ID設定
	 *
	 * @param string $itemId
	 */
	function setItemId($itemId) {
		$this->execTranRakutenIdInput->setItemId($itemId);
	}
	/**
	 * サブ商品ID設定
	 *
	 * @param string $itemSubId
	 */
	function setItemSubId($itemSubId) {
		$this->execTranRakutenIdInput->setItemSubId($itemSubId);
	}
	/**
	 * 商品名設定
	 *
	 * @param string $itemName
	 */
	function setItemName($itemName) {
		$this->execTranRakutenIdInput->setItemName($itemName);
	}
	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranRakutenIdInput->setRetURL($retURL);
	}
	/**
	 * 処理NG時URL設定
	 *
	 * @param string $errorRcvURL
	 */
	function setErrorRcvURL($errorRcvURL) {
		$this->execTranRakutenIdInput->setErrorRcvURL($errorRcvURL);
	}
	/**
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->execTranRakutenIdInput->setPaymentTermSec($paymentTermSec);
	}
	/**
	 * 複数商品設定
	 *
	 * @param string $multiItem
	 */
	function setMultiItem($multiItem) {
		$this->execTranRakutenIdInput->setMultiItem($multiItem);
	}

}
?>
