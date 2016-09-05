<?php
require_once 'com/gmo_pg/client/input/EntryTranMcpInput.php';
require_once 'com/gmo_pg/client/input/ExecTranMcpInput.php';
/**
 * <b>多通貨クレジットカード登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranMcpInput {

	/**
	 * @var EntryTranMcpInput 多通貨クレジットカード登録入力パラメータ
	 */
	var $entryTranMcpInput;/* @var $entryTranInput EntryTranMcpInput */

	/**
	 * @var ExecTranMcpInput 多通貨クレジットカード実行入力パラメータ
	 */
	var $execTranMcpInput;/* @var $execTranInput ExecTranMcpInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranMcpInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranMcpInput = new EntryTranMcpInput($params);
		$this->execTranMcpInput = new ExecTranMcpInput($params);
	}

	/**
	 * 多通貨クレジットカード取引登録入力パラメータ取得
	 *
	 * @return EntryTranMcpInput 取引登録時パラメータ
	 */
	function &getEntryTranMcpInput() {
		return $this->entryTranMcpInput;
	}

	/**
	 * 多通貨クレジットカード実行入力パラメータ取得
	 * @return ExecTranMcpInput 決済実行時パラメータ
	 */
	function &getExecTranMcpInput() {
		return $this->execTranMcpInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranMcpInput->getShopID();

	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranMcpInput->getShopPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranMcpInput->getOrderID();

	}
	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->entryTranMcpInput->getJobCd();
	}
	/**
	 * 商品コード取得
	 * @return string 商品コード
	 */
	function getItemCode() {
		return $this->entryTranMcpInput->getItemCode();
	}
	/**
	 * 通貨コード取得
	 * @return string 通貨コード
	 */
	function getCurrency() {
		return $this->entryTranMcpInput->getCurrency();
	}
	/**
	 * 利用料金取得
	 * @return bigDecimal 利用料金
	 */
	function getAmount() {
		return $this->entryTranMcpInput->getAmount();
	}
	/**
	 * 税送料取得
	 * @return bigDecimal 税送料
	 */
	function getTax() {
		return $this->entryTranMcpInput->getTax();
	}
	/**
	 * 3Dセキュア表示店舗名取得
	 * @return string 3Dセキュア表示店舗名
	 */
	function getTdTenantName() {
		return $this->entryTranMcpInput->getTdTenantName();
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranMcpInput->getAccessID();
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranMcpInput->getAccessPass();
	}
	/**
	 * カード番号取得
	 * @return string カード番号
	 */
	function getCardNo() {
		return $this->execTranMcpInput->getCardNo();
	}
	/**
	 * 有効期限取得
	 * @return string 有効期限
	 */
	function getExpire() {
		return $this->execTranMcpInput->getExpire();
	}
	/**
	 * セキュリティコード取得
	 * @return string セキュリティコード
	 */
	function getSecurityCode() {
		return $this->execTranMcpInput->getSecurityCode();
	}
	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->execTranMcpInput->getRetURL();
	}
	/**
	 * 処理NG時URL取得
	 * @return string 処理NG時URL
	 */
	function getErrorRcvURL() {
		return $this->execTranMcpInput->getErrorRcvURL();
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranMcpInput->getClientField1();
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranMcpInput->getClientField2();
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranMcpInput->getClientField3();
	}
	/**
	 * 加盟店自由項目返却フラグ取得
	 * @return string 加盟店自由項目返却フラグ
	 */
	function getClientFieldFlag() {
		return $this->execTranMcpInput->getClientFieldFlag();
	}
	/**
	 * 支払開始期限秒取得
	 * @return integer 支払開始期限秒
	 */
	function getPaymentTermSec() {
		return $this->execTranMcpInput->getPaymentTermSec();
	}

	/**
	 * 多通貨クレジットカード取引登録入力パラメータ設定
	 *
	 * @param EntryTranMcpInput entryTranMcpInput  取引登録入力パラメータ
	 */
	function setEntryTranMcpInput(&$entryTranMcpInput) {
		$this->entryTranMcpInput = $entryTranMcpInput;
	}

	/**
	 * 多通貨クレジットカード実行入力パラメータ設定
	 *
	 * @param ExecTranMcpInput  execTranMcpInput   決済実行入力パラメータ
	 */
	function setExecTranMcpInput(&$execTranMcpInput) {
		$this->execTranMcpInput = $execTranMcpInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranMcpInput->setShopID($shopID);
		$this->execTranMcpInput->setShopID($shopID);

	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranMcpInput->setShopPass($shopPass);
		$this->execTranMcpInput->setShopPass($shopPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranMcpInput->setOrderID($orderID);
		$this->execTranMcpInput->setOrderID($orderID);

	}
	/**
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->entryTranMcpInput->setJobCd($jobCd);
	}
	/**
	 * 商品コード設定
	 *
	 * @param string $itemCode
	 */
	function setItemCode($itemCode) {
		$this->entryTranMcpInput->setItemCode($itemCode);
	}
	/**
	 * 通貨コード設定
	 *
	 * @param string $currency
	 */
	function setCurrency($currency) {
		$this->entryTranMcpInput->setCurrency($currency);
	}
	/**
	 * 利用料金設定
	 *
	 * @param bigDecimal $amount
	 */
	function setAmount($amount) {
		$this->entryTranMcpInput->setAmount($amount);
	}
	/**
	 * 税送料設定
	 *
	 * @param bigDecimal $tax
	 */
	function setTax($tax) {
		$this->entryTranMcpInput->setTax($tax);
	}
	/**
	 * 3Dセキュア表示店舗名設定
	 *
	 * @param string $tdTenantName
	 */
	function setTdTenantName($tdTenantName) {
		$this->entryTranMcpInput->setTdTenantName($tdTenantName);
	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranMcpInput->setAccessID($accessID);
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranMcpInput->setAccessPass($accessPass);
	}
	/**
	 * カード番号設定
	 *
	 * @param string $cardNo
	 */
	function setCardNo($cardNo) {
		$this->execTranMcpInput->setCardNo($cardNo);
	}
	/**
	 * 有効期限設定
	 *
	 * @param string $expire
	 */
	function setExpire($expire) {
		$this->execTranMcpInput->setExpire($expire);
	}
	/**
	 * セキュリティコード設定
	 *
	 * @param string $securityCode
	 */
	function setSecurityCode($securityCode) {
		$this->execTranMcpInput->setSecurityCode($securityCode);
	}
	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranMcpInput->setRetURL($retURL);
	}
	/**
	 * 処理NG時URL設定
	 *
	 * @param string $errorRcvURL
	 */
	function setErrorRcvURL($errorRcvURL) {
		$this->execTranMcpInput->setErrorRcvURL($errorRcvURL);
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranMcpInput->setClientField1($clientField1);
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranMcpInput->setClientField2($clientField2);
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranMcpInput->setClientField3($clientField3);
	}
	/**
	 * 加盟店自由項目返却フラグ設定
	 *
	 * @param string $clientFieldFlag
	 */
	function setClientFieldFlag($clientFieldFlag) {
		$this->execTranMcpInput->setClientFieldFlag($clientFieldFlag);
	}
	/**
	 * 支払開始期限秒設定
	 *
	 * @param integer $paymentTermSec
	 */
	function setPaymentTermSec($paymentTermSec) {
		$this->execTranMcpInput->setPaymentTermSec($paymentTermSec);
	}

}
?>
