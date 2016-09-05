<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>LINE Pay決済実行　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranLinepayInput extends BaseInput {

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
	 * @var string サイトID
	 */
	var $siteID;
	/**
	 * @var string サイトパスワード
	 */
	var $sitePass;
	/**
	 * @var string 会員ID
	 */
	var $memberID;
	/**
	 * @var string 会員名
	 */
	var $memberName;
	/**
	 * @var string 会員登録フラグ
	 */
	var $createMember;
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
	 * @var string 決済結果戻しURL
	 */
	var $retURL;
	/**
	 * @var string 処理NG時URL
	 */
	var $errorRcvURL;
	/**
	 * @var string 商品画像URL
	 */
	var $productImageUrl;
	/**
	 * @var string LineメンバーID
	 */
	var $mid;
	/**
	 * @var string 受取人連絡先
	 */
	var $deliveryPlacePhone;
	/**
	 * @var string 決済待機画面用言語
	 */
	var $langCode;
	/**
	 * @var string 商品名
	 */
	var $productName;
	/**
	 * @var string phishing防止用情報(PackageName)
	 */
	var $packageName;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function ExecTranLinepayInput($params = null) {
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
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteID() {
		return $this->siteID;
	}
	/**
	 * サイトパスワード取得
	 * @return string サイトパスワード
	 */
	function getSitePass() {
		return $this->sitePass;
	}
	/**
	 * 会員ID取得
	 * @return string 会員ID
	 */
	function getMemberID() {
		return $this->memberID;
	}
	/**
	 * 会員名取得
	 * @return string 会員名
	 */
	function getMemberName() {
		return $this->memberName;
	}
	/**
	 * 会員登録フラグ取得
	 * @return string 会員登録フラグ
	 */
	function getCreateMember() {
		return $this->createMember;
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
	 * 商品画像URL取得
	 * @return string 商品画像URL
	 */
	function getProductImageUrl() {
		return $this->productImageUrl;
	}
	/**
	 * LineメンバーID取得
	 * @return string LineメンバーID
	 */
	function getMid() {
		return $this->mid;
	}
	/**
	 * 受取人連絡先取得
	 * @return string 受取人連絡先
	 */
	function getDeliveryPlacePhone() {
		return $this->deliveryPlacePhone;
	}
	/**
	 * 決済待機画面用言語取得
	 * @return string 決済待機画面用言語
	 */
	function getLangCode() {
		return $this->langCode;
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getProductName() {
		return $this->productName;
	}
	/**
	 * phishing防止用情報(PackageName)取得
	 * @return string phishing防止用情報(PackageName)
	 */
	function getPackageName() {
		return $this->packageName;
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
	 * サイトID設定
	 *
	 * @param string $siteID
	 */
	function setSiteID($siteID) {
		$this->siteID = $siteID;
	}
	/**
	 * サイトパスワード設定
	 *
	 * @param string $sitePass
	 */
	function setSitePass($sitePass) {
		$this->sitePass = $sitePass;
	}
	/**
	 * 会員ID設定
	 *
	 * @param string $memberID
	 */
	function setMemberID($memberID) {
		$this->memberID = $memberID;
	}
	/**
	 * 会員名設定
	 *
	 * @param string $memberName
	 */
	function setMemberName($memberName) {
		$this->memberName = $memberName;
	}
	/**
	 * 会員登録フラグ設定
	 *
	 * @param string $createMember
	 */
	function setCreateMember($createMember) {
		$this->createMember = $createMember;
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
	 * 商品画像URL設定
	 *
	 * @param string $productImageUrl
	 */
	function setProductImageUrl($productImageUrl) {
		$this->productImageUrl = $productImageUrl;
	}
	/**
	 * LineメンバーID設定
	 *
	 * @param string $mid
	 */
	function setMid($mid) {
		$this->mid = $mid;
	}
	/**
	 * 受取人連絡先設定
	 *
	 * @param string $deliveryPlacePhone
	 */
	function setDeliveryPlacePhone($deliveryPlacePhone) {
		$this->deliveryPlacePhone = $deliveryPlacePhone;
	}
	/**
	 * 決済待機画面用言語設定
	 *
	 * @param string $langCode
	 */
	function setLangCode($langCode) {
		$this->langCode = $langCode;
	}
	/**
	 * 商品名設定
	 *
	 * @param string $productName
	 */
	function setProductName($productName) {
		$this->productName = $productName;
	}
	/**
	 * phishing防止用情報(PackageName)設定
	 *
	 * @param string $packageName
	 */
	function setPackageName($packageName) {
		$this->packageName = $packageName;
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
		$this->setSiteID($this->getStringValue($params, 'SiteID', $this->getSiteID()));
		$this->setSitePass($this->getStringValue($params, 'SitePass', $this->getSitePass()));
		$this->setMemberID($this->getStringValue($params, 'MemberID', $this->getMemberID()));
		$this->setMemberName($this->getStringValue($params, 'MemberName', $this->getMemberName()));
		$this->setCreateMember($this->getStringValue($params, 'CreateMember', $this->getCreateMember()));
		$this->setClientField1($this->getStringValue($params, 'ClientField1', $this->getClientField1()));
		$this->setClientField2($this->getStringValue($params, 'ClientField2', $this->getClientField2()));
		$this->setClientField3($this->getStringValue($params, 'ClientField3', $this->getClientField3()));
		$this->setClientFieldFlag($this->getStringValue($params, 'ClientFieldFlag', $this->getClientFieldFlag()));
		$this->setRetURL($this->getStringValue($params, 'RetURL', $this->getRetURL()));
		$this->setErrorRcvURL($this->getStringValue($params, 'ErrorRcvURL', $this->getErrorRcvURL()));
		$this->setProductImageUrl($this->getStringValue($params, 'ProductImageUrl', $this->getProductImageUrl()));
		$this->setMid($this->getStringValue($params, 'Mid', $this->getMid()));
		$this->setDeliveryPlacePhone($this->getStringValue($params, 'DeliveryPlacePhone', $this->getDeliveryPlacePhone()));
		$this->setLangCode($this->getStringValue($params, 'LangCode', $this->getLangCode()));
		$this->setProductName($this->getStringValue($params, 'ProductName', $this->getProductName()));
		$this->setPackageName($this->getStringValue($params, 'PackageName', $this->getPackageName()));

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
		$str .= 'SiteID=' . $this->encodeStr($this->getSiteID());
		$str .='&';
		$str .= 'SitePass=' . $this->encodeStr($this->getSitePass());
		$str .='&';
		$str .= 'MemberID=' . $this->encodeStr($this->getMemberID());
		$str .='&';
		$str .= 'MemberName=' . $this->encodeStr($this->getMemberName());
		$str .='&';
		$str .= 'CreateMember=' . $this->encodeStr($this->getCreateMember());
		$str .='&';
		$str .= 'ClientField1=' . $this->encodeStr($this->getClientField1());
		$str .='&';
		$str .= 'ClientField2=' . $this->encodeStr($this->getClientField2());
		$str .='&';
		$str .= 'ClientField3=' . $this->encodeStr($this->getClientField3());
		$str .='&';
		$str .= 'ClientFieldFlag=' . $this->encodeStr($this->getClientFieldFlag());
		$str .='&';
		$str .= 'RetURL=' . $this->encodeStr($this->getRetURL());
		$str .='&';
		$str .= 'ErrorRcvURL=' . $this->encodeStr($this->getErrorRcvURL());
		$str .='&';
		$str .= 'ProductImageUrl=' . $this->encodeStr($this->getProductImageUrl());
		$str .='&';
		$str .= 'Mid=' . $this->encodeStr($this->getMid());
		$str .='&';
		$str .= 'DeliveryPlacePhone=' . $this->encodeStr($this->getDeliveryPlacePhone());
		$str .='&';
		$str .= 'LangCode=' . $this->encodeStr($this->getLangCode());
		$str .='&';
		$str .= 'ProductName=' . $this->encodeStr($this->getProductName());
		$str .='&';
		$str .= 'PackageName=' . $this->encodeStr($this->getPackageName());

	    return $str;
	}


}
?>
