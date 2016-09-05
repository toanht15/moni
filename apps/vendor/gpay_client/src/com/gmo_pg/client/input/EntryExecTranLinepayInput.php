<?php
require_once 'com/gmo_pg/client/input/EntryTranLinepayInput.php';
require_once 'com/gmo_pg/client/input/ExecTranLinepayInput.php';
/**
 * <b>LINE Pay登録・決済一括実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranLinepayInput {

	/**
	 * @var EntryTranLinepayInput LINE Pay登録入力パラメータ
	 */
	var $entryTranLinepayInput;/* @var $entryTranInput EntryTranLinepayInput */

	/**
	 * @var ExecTranLinepayInput LINE Pay実行入力パラメータ
	 */
	var $execTranLinepayInput;/* @var $execTranInput ExecTranLinepayInput */

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function EntryExecTranLinepayInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranLinepayInput = new EntryTranLinepayInput($params);
		$this->execTranLinepayInput = new ExecTranLinepayInput($params);
	}

	/**
	 * LINE Pay取引登録入力パラメータ取得
	 *
	 * @return EntryTranLinepayInput 取引登録時パラメータ
	 */
	function &getEntryTranLinepayInput() {
		return $this->entryTranLinepayInput;
	}

	/**
	 * LINE Pay実行入力パラメータ取得
	 * @return ExecTranLinepayInput 決済実行時パラメータ
	 */
	function &getExecTranLinepayInput() {
		return $this->execTranLinepayInput;
	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->entryTranLinepayInput->getShopID();

	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->entryTranLinepayInput->getShopPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranLinepayInput->getOrderID();

	}
	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->entryTranLinepayInput->getJobCd();
	}
	/**
	 * 利用料金取得
	 * @return bigDecimal 利用料金
	 */
	function getAmount() {
		return $this->entryTranLinepayInput->getAmount();
	}
	/**
	 * 税送料取得
	 * @return bigDecimal 税送料
	 */
	function getTax() {
		return $this->entryTranLinepayInput->getTax();
	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->execTranLinepayInput->getAccessID();
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->execTranLinepayInput->getAccessPass();
	}
	/**
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteID() {
		return $this->execTranLinepayInput->getSiteID();
	}
	/**
	 * サイトパスワード取得
	 * @return string サイトパスワード
	 */
	function getSitePass() {
		return $this->execTranLinepayInput->getSitePass();
	}
	/**
	 * 会員ID取得
	 * @return string 会員ID
	 */
	function getMemberID() {
		return $this->execTranLinepayInput->getMemberID();
	}
	/**
	 * 会員名取得
	 * @return string 会員名
	 */
	function getMemberName() {
		return $this->execTranLinepayInput->getMemberName();
	}
	/**
	 * 会員登録フラグ取得
	 * @return string 会員登録フラグ
	 */
	function getCreateMember() {
		return $this->execTranLinepayInput->getCreateMember();
	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranLinepayInput->getClientField1();
	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranLinepayInput->getClientField2();
	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranLinepayInput->getClientField3();
	}
	/**
	 * 加盟店自由項目返却フラグ取得
	 * @return string 加盟店自由項目返却フラグ
	 */
	function getClientFieldFlag() {
		return $this->execTranLinepayInput->getClientFieldFlag();
	}
	/**
	 * 決済結果戻しURL取得
	 * @return string 決済結果戻しURL
	 */
	function getRetURL() {
		return $this->execTranLinepayInput->getRetURL();
	}
	/**
	 * 処理NG時URL取得
	 * @return string 処理NG時URL
	 */
	function getErrorRcvURL() {
		return $this->execTranLinepayInput->getErrorRcvURL();
	}
	/**
	 * 商品画像URL取得
	 * @return string 商品画像URL
	 */
	function getProductImageUrl() {
		return $this->execTranLinepayInput->getProductImageUrl();
	}
	/**
	 * LineメンバーID取得
	 * @return string LineメンバーID
	 */
	function getMid() {
		return $this->execTranLinepayInput->getMid();
	}
	/**
	 * 受取人連絡先取得
	 * @return string 受取人連絡先
	 */
	function getDeliveryPlacePhone() {
		return $this->execTranLinepayInput->getDeliveryPlacePhone();
	}
	/**
	 * 決済待機画面用言語取得
	 * @return string 決済待機画面用言語
	 */
	function getLangCode() {
		return $this->execTranLinepayInput->getLangCode();
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getProductName() {
		return $this->execTranLinepayInput->getProductName();
	}
	/**
	 * phishing防止用情報(PackageName)取得
	 * @return string phishing防止用情報(PackageName)
	 */
	function getPackageName() {
		return $this->execTranLinepayInput->getPackageName();
	}

	/**
	 * LINE Pay取引登録入力パラメータ設定
	 *
	 * @param EntryTranLinepayInput entryTranLinepayInput  取引登録入力パラメータ
	 */
	function setEntryTranLinepayInput(&$entryTranLinepayInput) {
		$this->entryTranLinepayInput = $entryTranLinepayInput;
	}

	/**
	 * LINE Pay実行入力パラメータ設定
	 *
	 * @param ExecTranLinepayInput  execTranLinepayInput   決済実行入力パラメータ
	 */
	function setExecTranLinepayInput(&$execTranLinepayInput) {
		$this->execTranLinepayInput = $execTranLinepayInput;
	}

	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->entryTranLinepayInput->setShopID($shopID);
		$this->execTranLinepayInput->setShopID($shopID);

	}
	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->entryTranLinepayInput->setShopPass($shopPass);
		$this->execTranLinepayInput->setShopPass($shopPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranLinepayInput->setOrderID($orderID);
		$this->execTranLinepayInput->setOrderID($orderID);

	}
	/**
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->entryTranLinepayInput->setJobCd($jobCd);
	}
	/**
	 * 利用料金設定
	 *
	 * @param bigDecimal $amount
	 */
	function setAmount($amount) {
		$this->entryTranLinepayInput->setAmount($amount);
	}
	/**
	 * 税送料設定
	 *
	 * @param bigDecimal $tax
	 */
	function setTax($tax) {
		$this->entryTranLinepayInput->setTax($tax);
	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->execTranLinepayInput->setAccessID($accessID);
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->execTranLinepayInput->setAccessPass($accessPass);
	}
	/**
	 * サイトID設定
	 *
	 * @param string $siteID
	 */
	function setSiteID($siteID) {
		$this->execTranLinepayInput->setSiteID($siteID);
	}
	/**
	 * サイトパスワード設定
	 *
	 * @param string $sitePass
	 */
	function setSitePass($sitePass) {
		$this->execTranLinepayInput->setSitePass($sitePass);
	}
	/**
	 * 会員ID設定
	 *
	 * @param string $memberID
	 */
	function setMemberID($memberID) {
		$this->execTranLinepayInput->setMemberID($memberID);
	}
	/**
	 * 会員名設定
	 *
	 * @param string $memberName
	 */
	function setMemberName($memberName) {
		$this->execTranLinepayInput->setMemberName($memberName);
	}
	/**
	 * 会員登録フラグ設定
	 *
	 * @param string $createMember
	 */
	function setCreateMember($createMember) {
		$this->execTranLinepayInput->setCreateMember($createMember);
	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranLinepayInput->setClientField1($clientField1);
	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranLinepayInput->setClientField2($clientField2);
	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranLinepayInput->setClientField3($clientField3);
	}
	/**
	 * 加盟店自由項目返却フラグ設定
	 *
	 * @param string $clientFieldFlag
	 */
	function setClientFieldFlag($clientFieldFlag) {
		$this->execTranLinepayInput->setClientFieldFlag($clientFieldFlag);
	}
	/**
	 * 決済結果戻しURL設定
	 *
	 * @param string $retURL
	 */
	function setRetURL($retURL) {
		$this->execTranLinepayInput->setRetURL($retURL);
	}
	/**
	 * 処理NG時URL設定
	 *
	 * @param string $errorRcvURL
	 */
	function setErrorRcvURL($errorRcvURL) {
		$this->execTranLinepayInput->setErrorRcvURL($errorRcvURL);
	}
	/**
	 * 商品画像URL設定
	 *
	 * @param string $productImageUrl
	 */
	function setProductImageUrl($productImageUrl) {
		$this->execTranLinepayInput->setProductImageUrl($productImageUrl);
	}
	/**
	 * LineメンバーID設定
	 *
	 * @param string $mid
	 */
	function setMid($mid) {
		$this->execTranLinepayInput->setMid($mid);
	}
	/**
	 * 受取人連絡先設定
	 *
	 * @param string $deliveryPlacePhone
	 */
	function setDeliveryPlacePhone($deliveryPlacePhone) {
		$this->execTranLinepayInput->setDeliveryPlacePhone($deliveryPlacePhone);
	}
	/**
	 * 決済待機画面用言語設定
	 *
	 * @param string $langCode
	 */
	function setLangCode($langCode) {
		$this->execTranLinepayInput->setLangCode($langCode);
	}
	/**
	 * 商品名設定
	 *
	 * @param string $productName
	 */
	function setProductName($productName) {
		$this->execTranLinepayInput->setProductName($productName);
	}
	/**
	 * phishing防止用情報(PackageName)設定
	 *
	 * @param string $packageName
	 */
	function setPackageName($packageName) {
		$this->execTranLinepayInput->setPackageName($packageName);
	}

}
?>
