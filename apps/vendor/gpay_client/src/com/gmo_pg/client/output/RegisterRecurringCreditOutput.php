<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>自動売上自動売上登録　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class RegisterRecurringCreditOutput extends BaseOutput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string 自動売上ID
	 */
	var $recurringID;
	/**
	 * @var integer 利用金額
	 */
	var $amount;
	/**
	 * @var integer 税送料
	 */
	var $tax;
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
	 * @var string 次回課金日
	 */
	var $nextChargeDate;
	/**
	 * @var string 課金手段
	 */
	var $method;
	/**
	 * @var string カード番号
	 */
	var $cardNo;
	/**
	 * @var string カード有効期限
	 */
	var $expire;
	/**
	 * @var string サイトID
	 */
	var $siteID;
	/**
	 * @var string 会員ID
	 */
	var $memberID;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function RegisterRecurringCreditOutput($params = null) {
		$this->__construct($params);
	}

	
	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function __construct($params = null) {
		parent::__construct($params);
		
		// 引数が無い場合は戻る
		if (is_null($params)) {
            return;
        }
		
        // マップの展開
		$this->setShopID($params->get('ShopID'));
		$this->setRecurringID($params->get('RecurringID'));
		$this->setAmount($params->get('Amount'));
		$this->setTax($params->get('Tax'));
		$this->setChargeDay($params->get('ChargeDay'));
		$this->setChargeMonth($params->get('ChargeMonth'));
		$this->setChargeStartDate($params->get('ChargeStartDate'));
		$this->setChargeStopDate($params->get('ChargeStopDate'));
		$this->setNextChargeDate($params->get('NextChargeDate'));
		$this->setMethod($params->get('Method'));
		$this->setCardNo($params->get('CardNo'));
		$this->setExpire($params->get('Expire'));
		$this->setSiteID($params->get('SiteID'));
		$this->setMemberID($params->get('MemberID'));

	}

	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->shopID;
	}
	/**
	 * 自動売上ID取得
	 * @return string 自動売上ID
	 */
	function getRecurringID() {
		return $this->recurringID;
	}
	/**
	 * 利用金額取得
	 * @return integer 利用金額
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
	 * 次回課金日取得
	 * @return string 次回課金日
	 */
	function getNextChargeDate() {
		return $this->nextChargeDate;
	}
	/**
	 * 課金手段取得
	 * @return string 課金手段
	 */
	function getMethod() {
		return $this->method;
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
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteID() {
		return $this->siteID;
	}
	/**
	 * 会員ID取得
	 * @return string 会員ID
	 */
	function getMemberID() {
		return $this->memberID;
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
	 * 自動売上ID設定
	 *
	 * @param string $recurringID
	 */
	function setRecurringID($recurringID) {
		$this->recurringID = $recurringID;
	}
	/**
	 * 利用金額設定
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
	 * 次回課金日設定
	 *
	 * @param string $nextChargeDate
	 */
	function setNextChargeDate($nextChargeDate) {
		$this->nextChargeDate = $nextChargeDate;
	}
	/**
	 * 課金手段設定
	 *
	 * @param string $method
	 */
	function setMethod($method) {
		$this->method = $method;
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
	 * サイトID設定
	 *
	 * @param string $siteID
	 */
	function setSiteID($siteID) {
		$this->siteID = $siteID;
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
	 * 文字列表現
	 * <p>
	 *  現在の各パラメータを、パラメータ名=値&パラメータ名=値の形式で取得します。
	 * </p>
	 * @return string 出力パラメータの文字列表現
	 */
	function toString() {
		$str ='';
		$str .= 'ShopID=' . $this->encodeStr($this->getShopID());
		$str .='&';
		$str .= 'RecurringID=' . $this->encodeStr($this->getRecurringID());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'ChargeDay=' . $this->encodeStr($this->getChargeDay());
		$str .='&';
		$str .= 'ChargeMonth=' . $this->encodeStr($this->getChargeMonth());
		$str .='&';
		$str .= 'ChargeStartDate=' . $this->encodeStr($this->getChargeStartDate());
		$str .='&';
		$str .= 'ChargeStopDate=' . $this->encodeStr($this->getChargeStopDate());
		$str .='&';
		$str .= 'NextChargeDate=' . $this->encodeStr($this->getNextChargeDate());
		$str .='&';
		$str .= 'Method=' . $this->encodeStr($this->getMethod());
		$str .='&';
		$str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
		$str .='&';
		$str .= 'Expire=' . $this->encodeStr($this->getExpire());
		$str .='&';
		$str .= 'SiteID=' . $this->encodeStr($this->getSiteID());
		$str .='&';
		$str .= 'MemberID=' . $this->encodeStr($this->getMemberID());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
