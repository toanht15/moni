<?php
require_once 'com/gmo_pg/client/input/EntryTranNetcashInput.php';
require_once 'com/gmo_pg/client/input/ExecTranNetcashInput.php';
/**
 * <b>NET CASH登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranNetcashInput {

	/**
	 * @var EntryTranNetcashInput NET CASH登録入力パラメータ
	 */
	var $entryTranNetcashInput;/* @var $entryTranInput EntryTranNetcashInput */

	/**
	 * @var ExecTranNetcashInput NET CASH実行入力パラメータ
	 */
	var $execTranNetcashInput;/* @var $execTranInput ExecTranNetcashInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranNetcashInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranNetcashInput = new EntryTranNetcashInput($params);
		$this->execTranNetcashInput = new ExecTranNetcashInput($params);
	}

	/**
	 * NET CASH取引登録入力パラメータ取得
	 *
	 * @return EntryTranNetcashInput 取引登録時パラメータ
	 */
	function &getEntryTranNetcashInput() {
		return $this->entryTranNetcashInput;
	}

	/**
	 * NET CASH実行入力パラメータ取得
	 * @return ExecTranNetcashInput 決済実行時パラメータ
	 */
	function &getExecTranNetcashInput() {
		return $this->execTranNetcashInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranNetcashInput->getShopID();

	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranNetcashInput->getShopPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranNetcashInput->getOrderID();

	}
	/**
	 * 利用料金取得
	 * @return integer 利用料金
	 */
	function getAmount() {
		return $this->entryTranNetcashInput->getAmount();
	}
	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->entryTranNetcashInput->getTax();
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranNetcashInput->getAccessID();
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranNetcashInput->getAccessPass();
	}
	/**
	 * リダイレクトURL取得
	 * @return string リダイレクトURL
	 */
	function getRetURL() {
		return $this->execTranNetcashInput->getRetURL();
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranNetcashInput->getClientField1();
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranNetcashInput->getClientField2();
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranNetcashInput->getClientField3();
	}
	/**
	 * NET CASH決済方法取得
	 * @return string NET CASH決済方法
	 */
	function getNetCashPayType() {
		return $this->execTranNetcashInput->getNetCashPayType();
	}

	/**
	 * NET CASH取引登録入力パラメータ設定
	 *
	 * @param EntryTranNetcashInput entryTranNetcashInput  取引登録入力パラメータ
	 */
	function setEntryTranNetcashInput(&$entryTranNetcashInput) {
		$this->entryTranNetcashInput = $entryTranNetcashInput;
	}

	/**
	 * NET CASH実行入力パラメータ設定
	 *
	 * @param ExecTranNetcashInput  execTranNetcashInput   決済実行入力パラメータ
	 */
	function setExecTranNetcashInput(&$execTranNetcashInput) {
		$this->execTranNetcashInput = $execTranNetcashInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranNetcashInput->setShopID($shopID);
		$this->execTranNetcashInput->setShopID($shopID);

	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranNetcashInput->setShopPass($shopPass);
		$this->execTranNetcashInput->setShopPass($shopPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranNetcashInput->setOrderID($orderID);
		$this->execTranNetcashInput->setOrderID($orderID);

	}
	/**
	 * 利用料金設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->entryTranNetcashInput->setAmount($amount);
	}
	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->entryTranNetcashInput->setTax($tax);
	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranNetcashInput->setAccessID($accessID);
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranNetcashInput->setAccessPass($accessPass);
	}
	/**
	 * リダイレクトURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranNetcashInput->setRetURL($retURL);
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranNetcashInput->setClientField1($clientField1);
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranNetcashInput->setClientField2($clientField2);
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranNetcashInput->setClientField3($clientField3);
	}
	/**
	 * NET CASH決済方法設定
	 *
	 * @param string $netCashPayType
	 */
	function setNetCashPayType($netCashPayType) {
		$this->execTranNetcashInput->setNetCashPayType($netCashPayType);
	}

}
?>
