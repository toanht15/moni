<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>多通貨クレジットカード取引登録　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryTranMcpInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var string 処理区分
	 */
	var $jobCd;
	/**
	 * @var string 商品コード
	 */
	var $itemCode;
	/**
	 * @var string 通貨コード
	 */
	var $currency;
	/**
	 * @var bigDecimal 利用料金
	 */
	var $amount;
	/**
	 * @var bigDecimal 税送料
	 */
	var $tax;
	/**
	 * @var string 3Dセキュア表示店舗名
	 */
	var $tdTenantName;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function EntryTranMcpInput($params = null) {
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
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->orderID;
	}
	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->jobCd;
	}
	/**
	 * 商品コード取得
	 * @return string 商品コード
	 */
	function getItemCode() {
		return $this->itemCode;
	}
	/**
	 * 通貨コード取得
	 * @return string 通貨コード
	 */
	function getCurrency() {
		return $this->currency;
	}
	/**
	 * 利用料金取得
	 * @return bigDecimal 利用料金
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
	 * 3Dセキュア表示店舗名取得
	 * @return string 3Dセキュア表示店舗名
	 */
	function getTdTenantName() {
		return $this->tdTenantName;
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
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->orderID = $orderID;
	}
	/**
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->jobCd = $jobCd;
	}
	/**
	 * 商品コード設定
	 *
	 * @param string $itemCode
	 */
	function setItemCode($itemCode) {
		$this->itemCode = $itemCode;
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
	 * 利用料金設定
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
	 * 3Dセキュア表示店舗名設定
	 *
	 * @param string $tdTenantName
	 */
	function setTdTenantName($tdTenantName) {
		$this->tdTenantName = $tdTenantName;
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
		$this->setOrderID($this->getStringValue($params, 'OrderID', $this->getOrderID()));
		$this->setJobCd($this->getStringValue($params, 'JobCd', $this->getJobCd()));
		$this->setItemCode($this->getStringValue($params, 'ItemCode', $this->getItemCode()));
		$this->setCurrency($this->getStringValue($params, 'Currency', $this->getCurrency()));
		$this->setAmount($this->getStringValue($params, 'Amount', $this->getAmount()));
		$this->setTax($this->getStringValue($params, 'Tax', $this->getTax()));
		$this->setTdTenantName($this->getStringValue($params, 'TdTenantName', $this->getTdTenantName()));

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
		$str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .='&';
		$str .= 'JobCd=' . $this->encodeStr($this->getJobCd());
		$str .='&';
		$str .= 'ItemCode=' . $this->encodeStr($this->getItemCode());
		$str .='&';
		$str .= 'Currency=' . $this->encodeStr($this->getCurrency());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'TdTenantName=' . $this->encodeStr($this->getTdTenantName());

	    return $str;
	}


}
?>
