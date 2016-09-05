<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>自動売上自動売上登録　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class RegisterRecurringCreditInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string 自動売上ID
	 */
	var $recurringID;
	/**
	 * @var integer 課金金額
	 */
	var $amount;
	/**
	 * @var integer 税送料
	 */
	var $tax;
	/**
	 * @var string 対象種別
	 */
	var $registType;
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
	 * @var string カード番号
	 */
	var $cardNo;
	/**
	 * @var string カード有効期限
	 */
	var $expire;
	/**
	 * @var string 元取引オーダーID
	 */
	var $srcOrderID;
	/**
	 * @var string 課金日
	 */
	var $chargeDay;
	/**
	 * @var string 課金月
	 */
	var $chargeMonth;
	/**
	 * @var string 課金開始日
	 */
	var $chargeStartDate;
	/**
	 * @var string 課金停止日
	 */
	var $chargeStopDate;
	/**
	 * @var string 加盟店向け自由項目1
	 */
	var $clientField1;
	/**
	 * @var string 加盟店向け自由項目2
	 */
	var $clientField2;
	/**
	 * @var string 加盟店向け自由項目3
	 */
	var $clientField3;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function RegisterRecurringCreditInput($params = null) {
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
	 * 自動売上ID取得
	 * @return string 自動売上ID
	 */
	function getRecurringID() {
		return $this->recurringID;
	}
	/**
	 * 課金金額取得
	 * @return integer 課金金額
	 */
	function getAmount() {
		return $this->amount;
	}
	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->tax;
	}
	/**
	 * 対象種別取得
	 * @return string 対象種別
	 */
	function getRegistType() {
		return $this->registType;
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
	 * カード番号取得
	 * @return string カード番号
	 */
	function getCardNo() {
		return $this->cardNo;
	}
	/**
	 * カード有効期限取得
	 * @return string カード有効期限
	 */
	function getExpire() {
		return $this->expire;
	}
	/**
	 * 元取引オーダーID取得
	 * @return string 元取引オーダーID
	 */
	function getSrcOrderID() {
		return $this->srcOrderID;
	}
	/**
	 * 課金日取得
	 * @return string 課金日
	 */
	function getChargeDay() {
		return $this->chargeDay;
	}
	/**
	 * 課金月取得
	 * @return string 課金月
	 */
	function getChargeMonth() {
		return $this->chargeMonth;
	}
	/**
	 * 課金開始日取得
	 * @return string 課金開始日
	 */
	function getChargeStartDate() {
		return $this->chargeStartDate;
	}
	/**
	 * 課金停止日取得
	 * @return string 課金停止日
	 */
	function getChargeStopDate() {
		return $this->chargeStopDate;
	}
	/**
	 * 加盟店向け自由項目1取得
	 * @return string 加盟店向け自由項目1
	 */
	function getClientField1() {
		return $this->clientField1;
	}
	/**
	 * 加盟店向け自由項目2取得
	 * @return string 加盟店向け自由項目2
	 */
	function getClientField2() {
		return $this->clientField2;
	}
	/**
	 * 加盟店向け自由項目3取得
	 * @return string 加盟店向け自由項目3
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
	 * 自動売上ID設定
	 *
	 * @param string $recurringID
	 */
	function setRecurringID($recurringID) {
		$this->recurringID = $recurringID;
	}
	/**
	 * 課金金額設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->amount = $amount;
	}
	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->tax = $tax;
	}
	/**
	 * 対象種別設定
	 *
	 * @param string $registType
	 */
	function setRegistType($registType) {
		$this->registType = $registType;
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
	 * カード番号設定
	 *
	 * @param string $cardNo
	 */
	function setCardNo($cardNo) {
		$this->cardNo = $cardNo;
	}
	/**
	 * カード有効期限設定
	 *
	 * @param string $expire
	 */
	function setExpire($expire) {
		$this->expire = $expire;
	}
	/**
	 * 元取引オーダーID設定
	 *
	 * @param string $srcOrderID
	 */
	function setSrcOrderID($srcOrderID) {
		$this->srcOrderID = $srcOrderID;
	}
	/**
	 * 課金日設定
	 *
	 * @param string $chargeDay
	 */
	function setChargeDay($chargeDay) {
		$this->chargeDay = $chargeDay;
	}
	/**
	 * 課金月設定
	 *
	 * @param string $chargeMonth
	 */
	function setChargeMonth($chargeMonth) {
		$this->chargeMonth = $chargeMonth;
	}
	/**
	 * 課金開始日設定
	 *
	 * @param string $chargeStartDate
	 */
	function setChargeStartDate($chargeStartDate) {
		$this->chargeStartDate = $chargeStartDate;
	}
	/**
	 * 課金停止日設定
	 *
	 * @param string $chargeStopDate
	 */
	function setChargeStopDate($chargeStopDate) {
		$this->chargeStopDate = $chargeStopDate;
	}
	/**
	 * 加盟店向け自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->clientField1 = $clientField1;
	}
	/**
	 * 加盟店向け自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->clientField2 = $clientField2;
	}
	/**
	 * 加盟店向け自由項目3設定
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
		$this->setRecurringID($this->getStringValue($params, 'RecurringID', $this->getRecurringID()));
		$this->setAmount($this->getStringValue($params, 'Amount', $this->getAmount()));
		$this->setTax($this->getStringValue($params, 'Tax', $this->getTax()));
		$this->setRegistType($this->getStringValue($params, 'RegistType', $this->getRegistType()));
		$this->setSiteID($this->getStringValue($params, 'SiteID', $this->getSiteID()));
		$this->setSitePass($this->getStringValue($params, 'SitePass', $this->getSitePass()));
		$this->setMemberID($this->getStringValue($params, 'MemberID', $this->getMemberID()));
		$this->setCardNo($this->getStringValue($params, 'CardNo', $this->getCardNo()));
		$this->setExpire($this->getStringValue($params, 'Expire', $this->getExpire()));
		$this->setSrcOrderID($this->getStringValue($params, 'SrcOrderID', $this->getSrcOrderID()));
		$this->setChargeDay($this->getStringValue($params, 'ChargeDay', $this->getChargeDay()));
		$this->setChargeMonth($this->getStringValue($params, 'ChargeMonth', $this->getChargeMonth()));
		$this->setChargeStartDate($this->getStringValue($params, 'ChargeStartDate', $this->getChargeStartDate()));
		$this->setChargeStopDate($this->getStringValue($params, 'ChargeStopDate', $this->getChargeStopDate()));
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
		$str .= 'RecurringID=' . $this->encodeStr($this->getRecurringID());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'RegistType=' . $this->encodeStr($this->getRegistType());
		$str .='&';
		$str .= 'SiteID=' . $this->encodeStr($this->getSiteID());
		$str .='&';
		$str .= 'SitePass=' . $this->encodeStr($this->getSitePass());
		$str .='&';
		$str .= 'MemberID=' . $this->encodeStr($this->getMemberID());
		$str .='&';
		$str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
		$str .='&';
		$str .= 'Expire=' . $this->encodeStr($this->getExpire());
		$str .='&';
		$str .= 'SrcOrderID=' . $this->encodeStr($this->getSrcOrderID());
		$str .='&';
		$str .= 'ChargeDay=' . $this->encodeStr($this->getChargeDay());
		$str .='&';
		$str .= 'ChargeMonth=' . $this->encodeStr($this->getChargeMonth());
		$str .='&';
		$str .= 'ChargeStartDate=' . $this->encodeStr($this->getChargeStartDate());
		$str .='&';
		$str .= 'ChargeStopDate=' . $this->encodeStr($this->getChargeStopDate());
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
