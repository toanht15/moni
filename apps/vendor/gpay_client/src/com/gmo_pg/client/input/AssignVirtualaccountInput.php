<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>銀行振込(バーチャル口座)継続口座登録　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class AssignVirtualaccountInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string 継続ID
	 */
	var $reserveID;
	/**
	 * @var string 銀行コード
	 */
	var $bankCode;
	/**
	 * @var string 支店コード
	 */
	var $branchCode;
	/**
	 * @var string 科目
	 */
	var $accountType;
	/**
	 * @var string 口座番号
	 */
	var $accountNumber;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function AssignVirtualaccountInput($params = null) {
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
	 * 継続ID取得
	 * @return string 継続ID
	 */
	function getReserveID() {
		return $this->reserveID;
	}
	/**
	 * 銀行コード取得
	 * @return string 銀行コード
	 */
	function getBankCode() {
		return $this->bankCode;
	}
	/**
	 * 支店コード取得
	 * @return string 支店コード
	 */
	function getBranchCode() {
		return $this->branchCode;
	}
	/**
	 * 科目取得
	 * @return string 科目
	 */
	function getAccountType() {
		return $this->accountType;
	}
	/**
	 * 口座番号取得
	 * @return string 口座番号
	 */
	function getAccountNumber() {
		return $this->accountNumber;
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
	 * 継続ID設定
	 *
	 * @param string $reserveID
	 */
	function setReserveID($reserveID) {
		$this->reserveID = $reserveID;
	}
	/**
	 * 銀行コード設定
	 *
	 * @param string $bankCode
	 */
	function setBankCode($bankCode) {
		$this->bankCode = $bankCode;
	}
	/**
	 * 支店コード設定
	 *
	 * @param string $branchCode
	 */
	function setBranchCode($branchCode) {
		$this->branchCode = $branchCode;
	}
	/**
	 * 科目設定
	 *
	 * @param string $accountType
	 */
	function setAccountType($accountType) {
		$this->accountType = $accountType;
	}
	/**
	 * 口座番号設定
	 *
	 * @param string $accountNumber
	 */
	function setAccountNumber($accountNumber) {
		$this->accountNumber = $accountNumber;
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
		$this->setReserveID($this->getStringValue($params, 'ReserveID', $this->getReserveID()));
		$this->setBankCode($this->getStringValue($params, 'BankCode', $this->getBankCode()));
		$this->setBranchCode($this->getStringValue($params, 'BranchCode', $this->getBranchCode()));
		$this->setAccountType($this->getStringValue($params, 'AccountType', $this->getAccountType()));
		$this->setAccountNumber($this->getStringValue($params, 'AccountNumber', $this->getAccountNumber()));

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
		$str .= 'ReserveID=' . $this->encodeStr($this->getReserveID());
		$str .='&';
		$str .= 'BankCode=' . $this->encodeStr($this->getBankCode());
		$str .='&';
		$str .= 'BranchCode=' . $this->encodeStr($this->getBranchCode());
		$str .='&';
		$str .= 'AccountType=' . $this->encodeStr($this->getAccountType());
		$str .='&';
		$str .= 'AccountNumber=' . $this->encodeStr($this->getAccountNumber());

	    return $str;
	}


}
?>
