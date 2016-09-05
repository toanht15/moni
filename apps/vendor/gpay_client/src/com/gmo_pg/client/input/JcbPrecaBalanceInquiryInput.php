<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>JCBプリカ残高照会　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class JcbPrecaBalanceInquiryInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string カード番号
	 */
	var $cardNo;
	/**
	 * @var string 認証番号
	 */
	var $approvalNo;

	
	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function JcbPrecaBalanceInquiryInput($params = null) {
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
	 * カード番号取得
	 * @return string カード番号
	 */
	function getCardNo() {
		return $this->cardNo;
	}
	/**
	 * 認証番号取得
	 * @return string 認証番号
	 */
	function getApprovalNo() {
		return $this->approvalNo;
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
	 * カード番号設定
	 *
	 * @param string $cardNo
	 */
	function setCardNo($cardNo) {
		$this->cardNo = $cardNo;
	}
	/**
	 * 認証番号設定
	 *
	 * @param string $approvalNo
	 */
	function setApprovalNo($approvalNo) {
		$this->approvalNo = $approvalNo;
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
		$this->setCardNo($this->getStringValue($params, 'CardNo', $this->getCardNo()));
		$this->setApprovalNo($this->getStringValue($params, 'ApprovalNo', $this->getApprovalNo()));

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
		$str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
		$str .='&';
		$str .= 'ApprovalNo=' . $this->encodeStr($this->getApprovalNo());

	    return $str;
	}


}
?>
