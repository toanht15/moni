<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>メールリンク決済開始　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class MaillinkstartInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string 実行モード
	 */
	var $execMode;
	/**
	 * @var string メールリンク注文番号
	 */
	var $mailLinkOrderNo;
	/**
	 * @var string 商品名
	 */
	var $itemName;
	/**
	 * @var string 通貨コード
	 */
	var $currency;
	/**
	 * @var bigDecimal 利用金額
	 */
	var $amount;
	/**
	 * @var bigDecimal 税送料
	 */
	var $tax;
	/**
	 * @var string 購入者氏名
	 */
	var $customerName;
	/**
	 * @var string メールアドレス
	 */
	var $mailAddress;
	/**
	 * @var integer テンプレートNo.
	 */
	var $templateNo;
	/**
	 * @var string メッセージ言語
	 */
	var $lang;
	/**
	 * @var integer 有効日数
	 */
	var $expireDays;
	/**
	 * @var string 加盟店自由項目１
	 */
	var $clientField1;
	/**
	 * @var string 加盟店自由項目２
	 */
	var $clientField2;
	/**
	 * @var string 加盟店自由項目３
	 */
	var $clientField3;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function MaillinkstartInput($params = null) {
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
	 * 実行モード取得
	 * @return string 実行モード
	 */
	function getExecMode() {
		return $this->execMode;
	}
	/**
	 * メールリンク注文番号取得
	 * @return string メールリンク注文番号
	 */
	function getMailLinkOrderNo() {
		return $this->mailLinkOrderNo;
	}
	/**
	 * 商品名取得
	 * @return string 商品名
	 */
	function getItemName() {
		return $this->itemName;
	}
	/**
	 * 通貨コード取得
	 * @return string 通貨コード
	 */
	function getCurrency() {
		return $this->currency;
	}
	/**
	 * 利用金額取得
	 * @return bigDecimal 利用金額
	 */
	function getAmount() {
		return $this->amount;
	}
	/**
	 * 税送料取得
	 * @return bigDecimal 税送料
	 */
	function getTax() {
		return $this->tax;
	}
	/**
	 * 購入者氏名取得
	 * @return string 購入者氏名
	 */
	function getCustomerName() {
		return $this->customerName;
	}
	/**
	 * メールアドレス取得
	 * @return string メールアドレス
	 */
	function getMailAddress() {
		return $this->mailAddress;
	}
	/**
	 * テンプレートNo.取得
	 * @return integer テンプレートNo.
	 */
	function getTemplateNo() {
		return $this->templateNo;
	}
	/**
	 * メッセージ言語取得
	 * @return string メッセージ言語
	 */
	function getLang() {
		return $this->lang;
	}
	/**
	 * 有効日数取得
	 * @return integer 有効日数
	 */
	function getExpireDays() {
		return $this->expireDays;
	}
	/**
	 * 加盟店自由項目１取得
	 * @return string 加盟店自由項目１
	 */
	function getClientField1() {
		return $this->clientField1;
	}
	/**
	 * 加盟店自由項目２取得
	 * @return string 加盟店自由項目２
	 */
	function getClientField2() {
		return $this->clientField2;
	}
	/**
	 * 加盟店自由項目３取得
	 * @return string 加盟店自由項目３
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
	 * 実行モード設定
	 *
	 * @param string $execMode
	 */
	function setExecMode($execMode) {
		$this->execMode = $execMode;
	}
	/**
	 * メールリンク注文番号設定
	 *
	 * @param string $mailLinkOrderNo
	 */
	function setMailLinkOrderNo($mailLinkOrderNo) {
		$this->mailLinkOrderNo = $mailLinkOrderNo;
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
	 * 通貨コード設定
	 *
	 * @param string $currency
	 */
	function setCurrency($currency) {
		$this->currency = $currency;
	}
	/**
	 * 利用金額設定
	 *
	 * @param bigDecimal $amount
	 */
	function setAmount($amount) {
		$this->amount = $amount;
	}
	/**
	 * 税送料設定
	 *
	 * @param bigDecimal $tax
	 */
	function setTax($tax) {
		$this->tax = $tax;
	}
	/**
	 * 購入者氏名設定
	 *
	 * @param string $customerName
	 */
	function setCustomerName($customerName) {
		$this->customerName = $customerName;
	}
	/**
	 * メールアドレス設定
	 *
	 * @param string $mailAddress
	 */
	function setMailAddress($mailAddress) {
		$this->mailAddress = $mailAddress;
	}
	/**
	 * テンプレートNo.設定
	 *
	 * @param integer $templateNo
	 */
	function setTemplateNo($templateNo) {
		$this->templateNo = $templateNo;
	}
	/**
	 * メッセージ言語設定
	 *
	 * @param string $lang
	 */
	function setLang($lang) {
		$this->lang = $lang;
	}
	/**
	 * 有効日数設定
	 *
	 * @param integer $expireDays
	 */
	function setExpireDays($expireDays) {
		$this->expireDays = $expireDays;
	}
	/**
	 * 加盟店自由項目１設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->clientField1 = $clientField1;
	}
	/**
	 * 加盟店自由項目２設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->clientField2 = $clientField2;
	}
	/**
	 * 加盟店自由項目３設定
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
		$this->setExecMode($this->getStringValue($params, 'ExecMode', $this->getExecMode()));
		$this->setMailLinkOrderNo($this->getStringValue($params, 'MailLinkOrderNo', $this->getMailLinkOrderNo()));
		$this->setItemName($this->getStringValue($params, 'ItemName', $this->getItemName()));
		$this->setCurrency($this->getStringValue($params, 'Currency', $this->getCurrency()));
		$this->setAmount($this->getStringValue($params, 'Amount', $this->getAmount()));
		$this->setTax($this->getStringValue($params, 'Tax', $this->getTax()));
		$this->setCustomerName($this->getStringValue($params, 'CustomerName', $this->getCustomerName()));
		$this->setMailAddress($this->getStringValue($params, 'MailAddress', $this->getMailAddress()));
		$this->setTemplateNo($this->getStringValue($params, 'TemplateNo', $this->getTemplateNo()));
		$this->setLang($this->getStringValue($params, 'Lang', $this->getLang()));
		$this->setExpireDays($this->getStringValue($params, 'ExpireDays', $this->getExpireDays()));
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
		$str .= 'ExecMode=' . $this->encodeStr($this->getExecMode());
		$str .='&';
		$str .= 'MailLinkOrderNo=' . $this->encodeStr($this->getMailLinkOrderNo());
		$str .='&';
		$str .= 'ItemName=' . $this->encodeStr($this->getItemName());
		$str .='&';
		$str .= 'Currency=' . $this->encodeStr($this->getCurrency());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'CustomerName=' . $this->encodeStr($this->getCustomerName());
		$str .='&';
		$str .= 'MailAddress=' . $this->encodeStr($this->getMailAddress());
		$str .='&';
		$str .= 'TemplateNo=' . $this->encodeStr($this->getTemplateNo());
		$str .='&';
		$str .= 'Lang=' . $this->encodeStr($this->getLang());
		$str .='&';
		$str .= 'ExpireDays=' . $this->encodeStr($this->getExpireDays());
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
