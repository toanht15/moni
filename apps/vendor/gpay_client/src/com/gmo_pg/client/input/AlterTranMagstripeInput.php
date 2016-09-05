<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>クレジットカード決済決済変更　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class AlterTranMagstripeInput extends BaseInput {

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
	 * @var string 処理区分
	 */
	var $jobCd;
	/**
	 * @var integer 利用金額
	 */
	var $amount;
	/**
	 * @var string 利用日(YYMMDD)
	 */
	var $displayDate;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function AlterTranMagstripeInput($params = null) {
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
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd() {
		return $this->jobCd;
	}
	/**
	 * 利用金額取得
	 * @return integer 利用金額
	 */
	function getAmount() {
		return $this->amount;
	}
	/**
	 * 利用日(YYMMDD)取得
	 * @return string 利用日(YYMMDD)
	 */
	function getDisplayDate() {
		return $this->displayDate;
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
	 * 処理区分設定
	 *
	 * @param string $jobCd
	 */
	function setJobCd($jobCd) {
		$this->jobCd = $jobCd;
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
	 * 利用日(YYMMDD)設定
	 *
	 * @param string $displayDate
	 */
	function setDisplayDate($displayDate) {
		$this->displayDate = $displayDate;
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
		$this->setJobCd($this->getStringValue($params, 'JobCd', $this->getJobCd()));
		$this->setAmount($this->getStringValue($params, 'Amount', $this->getAmount()));
		$this->setDisplayDate($this->getStringValue($params, 'DisplayDate', $this->getDisplayDate()));

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
		$str .= 'JobCd=' . $this->encodeStr($this->getJobCd());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'DisplayDate=' . $this->encodeStr($this->getDisplayDate());

	    return $str;
	}


}
?>
