<?php
require_once 'com/gmo_pg/client/input/EntryTranRecruitContinuanceInput.php';
require_once 'com/gmo_pg/client/input/ExecTranRecruitContinuanceInput.php';
/**
 * <b>リクルートかんたん支払い継続課金登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRecruitContinuanceInput {

	/**
	 * @var EntryTranRecruitContinuanceInput リクルートかんたん支払い継続課金登録入力パラメータ
	 */
	var $entryTranRecruitContinuanceInput;/* @var $entryTranInput EntryTranRecruitContinuanceInput */

	/**
	 * @var ExecTranRecruitContinuanceInput リクルートかんたん支払い継続課金実行入力パラメータ
	 */
	var $execTranRecruitContinuanceInput;/* @var $execTranInput ExecTranRecruitContinuanceInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranRecruitContinuanceInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranRecruitContinuanceInput = new EntryTranRecruitContinuanceInput($params);
		$this->execTranRecruitContinuanceInput = new ExecTranRecruitContinuanceInput($params);
	}

	/**
	 * リクルートかんたん支払い継続課金取引登録入力パラメータ取得
	 *
	 * @return EntryTranRecruitContinuanceInput 取引登録時パラメータ
	 */
	function &getEntryTranRecruitContinuanceInput() {
		return $this->entryTranRecruitContinuanceInput;
	}

	/**
	 * リクルートかんたん支払い継続課金実行入力パラメータ取得
	 * @return ExecTranRecruitContinuanceInput 決済実行時パラメータ
	 */
	function &getExecTranRecruitContinuanceInput() {
		return $this->execTranRecruitContinuanceInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranRecruitContinuanceInput->getShopID();

	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranRecruitContinuanceInput->getShopPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranRecruitContinuanceInput->getOrderID();

	}
	/**
	 * 利用料金取得
	 * @return integer 利用料金
	 */
	function getAmount() {
		return $this->entryTranRecruitContinuanceInput->getAmount();
	}
	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->entryTranRecruitContinuanceInput->getTax();
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranRecruitContinuanceInput->getAccessID();
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranRecruitContinuanceInput->getAccessPass();
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranRecruitContinuanceInput->getClientField1();
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranRecruitContinuanceInput->getClientField2();
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranRecruitContinuanceInput->getClientField3();
	}
	/**
	 * 加盟店自由項目返却フラグ取得
	 * @return string 加盟店自由項目返却フラグ
	 */
	function getClientFieldFlag() {
		return $this->execTranRecruitContinuanceInput->getClientFieldFlag();
	}
	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->execTranRecruitContinuanceInput->getRetURL();
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getItemName() {
		return $this->execTranRecruitContinuanceInput->getItemName();
	}
	/**
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->execTranRecruitContinuanceInput->getPaymentTermSec();
	}
	/**
	 * 課金基準日取得
	 * @return string 課金基準日
	 */
	function getChargeDay() {
		return $this->execTranRecruitContinuanceInput->getChargeDay();
	}
	/**
	 * 初月無料フラグ取得
	 * @return string 初月無料フラグ
	 */
	function getFirstMonthFreeFlag() {
		return $this->execTranRecruitContinuanceInput->getFirstMonthFreeFlag();
	}

	/**
	 * リクルートかんたん支払い継続課金取引登録入力パラメータ設定
	 *
	 * @param EntryTranRecruitContinuanceInput entryTranRecruitContinuanceInput  取引登録入力パラメータ
	 */
	function setEntryTranRecruitContinuanceInput(&$entryTranRecruitContinuanceInput) {
		$this->entryTranRecruitContinuanceInput = $entryTranRecruitContinuanceInput;
	}

	/**
	 * リクルートかんたん支払い継続課金実行入力パラメータ設定
	 *
	 * @param ExecTranRecruitContinuanceInput  execTranRecruitContinuanceInput   決済実行入力パラメータ
	 */
	function setExecTranRecruitContinuanceInput(&$execTranRecruitContinuanceInput) {
		$this->execTranRecruitContinuanceInput = $execTranRecruitContinuanceInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranRecruitContinuanceInput->setShopID($shopID);
		$this->execTranRecruitContinuanceInput->setShopID($shopID);

	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranRecruitContinuanceInput->setShopPass($shopPass);
		$this->execTranRecruitContinuanceInput->setShopPass($shopPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranRecruitContinuanceInput->setOrderID($orderID);
		$this->execTranRecruitContinuanceInput->setOrderID($orderID);

	}
	/**
	 * 利用料金設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->entryTranRecruitContinuanceInput->setAmount($amount);
	}
	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->entryTranRecruitContinuanceInput->setTax($tax);
	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranRecruitContinuanceInput->setAccessID($accessID);
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranRecruitContinuanceInput->setAccessPass($accessPass);
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranRecruitContinuanceInput->setClientField1($clientField1);
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranRecruitContinuanceInput->setClientField2($clientField2);
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranRecruitContinuanceInput->setClientField3($clientField3);
	}
	/**
	 * 加盟店自由項目返却フラグ設定
	 *
	 * @param string $clientFieldFlag
	 */
	function setClientFieldFlag($clientFieldFlag) {
		$this->execTranRecruitContinuanceInput->setClientFieldFlag($clientFieldFlag);
	}
	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranRecruitContinuanceInput->setRetURL($retURL);
	}
	/**
	 * 商品名設定
	 *
	 * @param string $itemName
	 */
	function setItemName($itemName) {
		$this->execTranRecruitContinuanceInput->setItemName($itemName);
	}
	/**
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->execTranRecruitContinuanceInput->setPaymentTermSec($paymentTermSec);
	}
	/**
	 * 課金基準日設定
	 *
	 * @param string $chargeDay
	 */
	function setChargeDay($chargeDay) {
		$this->execTranRecruitContinuanceInput->setChargeDay($chargeDay);
	}
	/**
	 * 初月無料フラグ設定
	 *
	 * @param string $firstMonthFreeFlag
	 */
	function setFirstMonthFreeFlag($firstMonthFreeFlag) {
		$this->execTranRecruitContinuanceInput->setFirstMonthFreeFlag($firstMonthFreeFlag);
	}

}
?>
