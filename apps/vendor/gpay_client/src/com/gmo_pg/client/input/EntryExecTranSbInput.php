<?php
require_once 'com/gmo_pg/client/input/EntryTranSbInput.php';
require_once 'com/gmo_pg/client/input/ExecTranSbInput.php';
/**
 * <b>ソフトバンクケータイ支払い登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/18
 */
class EntryExecTranSbInput {

	/**
	 * @var EntryTranSbInput ソフトバンクケータイ支払い登録入力パラメータ
	 */
	var $entryTranSbInput;/* @var $entryTranInput EntryTranSbInput */

	/**
	 * @var ExecTranSbInput ソフトバンクケータイ支払い実行入力パラメータ
	 */
	var $execTranSbInput;/* @var $execTranInput ExecTranSbInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranSbInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranSbInput = new EntryTranSbInput($params);
		$this->execTranSbInput = new ExecTranSbInput($params);
	}

	/**
	 * ソフトバンクケータイ支払い取引登録入力パラメータ取得
	 *
	 * @return EntryTranSbInput 取引登録時パラメータ
	 */
	function &getEntryTranSbInput() {
		return $this->entryTranSbInput;
	}

	/**
	 * ソフトバンクケータイ支払い実行入力パラメータ取得
	 * @return ExecTranSbInput 決済実行時パラメータ
	 */
	function &getExecTranSbInput() {
		return $this->execTranSbInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranSbInput->getShopID();
	}

	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranSbInput->getShopPass();
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranSbInput->getOrderID();
	}

	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->entryTranSbInput->getJobCd();
	}

	/**
	 * 利用金額取得
	 * @return string 利用金額
	 */
	function getAmount() {
		return $this->entryTranSbInput->getAmount();
	}

	/**
	 * 税送料取得
	 * @return string 税送料
	 */
	function getTax() {
		return $this->entryTranSbInput->getTax();
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranSbInput->getAccessID();
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranSbInput->getAccessPass();
	}

	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranSbInput->getClientField1();
	}

	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranSbInput->getClientField2();
	}

	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranSbInput->getClientField3();
	}

	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->execTranSbInput->getRetURL();
	}

	/**
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->execTranSbInput->getPaymentTermSec();
	}

	/**
	 * ソフトバンクケータイ支払い取引登録入力パラメータ設定
	 *
	 * @param EntryTranSbInput entryTranSbInput  取引登録入力パラメータ
	 */
	function setEntryTranSbInput(&$entryTranSbInput) {
		$this->entryTranSbInput = $entryTranSbInput;
	}

	/**
	 * ソフトバンクケータイ支払い実行入力パラメータ設定
	 *
	 * @param ExecTranSbInput  execTranSbInput   決済実行入力パラメータ
	 */
	function setExecTranSbInput(&$execTranSbInput) {
		$this->execTranSbInput = $execTranSbInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranSbInput->setShopID($shopID);
		$this->execTranSbInput->setShopID($shopID);
	}

	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranSbInput->setShopPass($shopPass);
		$this->execTranSbInput->setShopPass($shopPass);
	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranSbInput->setOrderID($orderID);
		$this->execTranSbInput->setOrderID($orderID);
	}

	/**
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->entryTranSbInput->setJobCd($jobCd);
	}

	/**
	 * 利用金額設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->entryTranSbInput->setAmount($amount);
	}

	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->entryTranSbInput->setTax($tax);
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranSbInput->setAccessID($accessID);
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranSbInput->setAccessPass($accessPass);
	}

	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranSbInput->setClientField1($clientField1);
	}

	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranSbInput->setClientField2($clientField2);
	}

	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranSbInput->setClientField3($clientField3);
	}

	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranSbInput->setRetURL($retURL);
	}

	/**
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->execTranSbInput->setPaymentTermSec($paymentTermSec);
	}
}
?>
