<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>JCBプリカ決済実行　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranJcbPrecaOutput extends BaseOutput {

	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var string 現状態
	 */
	var $status;
	/**
	 * @var integer 利用金額
	 */
	var $amount;
	/**
	 * @var integer 税送料
	 */
	var $tax;
	/**
	 * @var integer 利用前残高
	 */
	var $beforeBalance;
	/**
	 * @var integer 利用後残高
	 */
	var $afterBalance;
	/**
	 * @var string カードアクティベートステータス
	 */
	var $cardActivateStatus;
	/**
	 * @var string カード有効期限ステータス
	 */
	var $cardTermStatus;
	/**
	 * @var string カード有効ステータス
	 */
	var $cardInvalidStatus;
	/**
	 * @var string カードWEB参照ステータス
	 */
	var $cardWebInquiryStatus;
	/**
	 * @var string カード有効期限
	 */
	var $cardValidLimit;
	/**
	 * @var string 券種コード
	 */
	var $cardTypeCode;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function ExecTranJcbPrecaOutput($params = null) {
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
		$this->setOrderID($params->get('OrderID'));
		$this->setStatus($params->get('Status'));
		$this->setAmount($params->get('Amount'));
		$this->setTax($params->get('Tax'));
		$this->setBeforeBalance($params->get('BeforeBalance'));
		$this->setAfterBalance($params->get('AfterBalance'));
		$this->setCardActivateStatus($params->get('CardActivateStatus'));
		$this->setCardTermStatus($params->get('CardTermStatus'));
		$this->setCardInvalidStatus($params->get('CardInvalidStatus'));
		$this->setCardWebInquiryStatus($params->get('CardWebInquiryStatus'));
		$this->setCardValidLimit($params->get('CardValidLimit'));
		$this->setCardTypeCode($params->get('CardTypeCode'));

	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->orderID;
	}
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->status;
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
	 * 利用前残高取得
	 * @return integer 利用前残高
	 */
	function getBeforeBalance() {
		return $this->beforeBalance;
	}
	/**
	 * 利用後残高取得
	 * @return integer 利用後残高
	 */
	function getAfterBalance() {
		return $this->afterBalance;
	}
	/**
	 * カードアクティベートステータス取得
	 * @return string カードアクティベートステータス
	 */
	function getCardActivateStatus() {
		return $this->cardActivateStatus;
	}
	/**
	 * カード有効期限ステータス取得
	 * @return string カード有効期限ステータス
	 */
	function getCardTermStatus() {
		return $this->cardTermStatus;
	}
	/**
	 * カード有効ステータス取得
	 * @return string カード有効ステータス
	 */
	function getCardInvalidStatus() {
		return $this->cardInvalidStatus;
	}
	/**
	 * カードWEB参照ステータス取得
	 * @return string カードWEB参照ステータス
	 */
	function getCardWebInquiryStatus() {
		return $this->cardWebInquiryStatus;
	}
	/**
	 * カード有効期限取得
	 * @return string カード有効期限
	 */
	function getCardValidLimit() {
		return $this->cardValidLimit;
	}
	/**
	 * 券種コード取得
	 * @return string 券種コード
	 */
	function getCardTypeCode() {
		return $this->cardTypeCode;
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
	 * 現状態設定
	 *
	 * @param string $status
	 */
	function setStatus($status) {
		$this->status = $status;
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
	 * 利用前残高設定
	 *
	 * @param integer $beforeBalance
	 */
	function setBeforeBalance($beforeBalance) {
		$this->beforeBalance = $beforeBalance;
	}
	/**
	 * 利用後残高設定
	 *
	 * @param integer $afterBalance
	 */
	function setAfterBalance($afterBalance) {
		$this->afterBalance = $afterBalance;
	}
	/**
	 * カードアクティベートステータス設定
	 *
	 * @param string $cardActivateStatus
	 */
	function setCardActivateStatus($cardActivateStatus) {
		$this->cardActivateStatus = $cardActivateStatus;
	}
	/**
	 * カード有効期限ステータス設定
	 *
	 * @param string $cardTermStatus
	 */
	function setCardTermStatus($cardTermStatus) {
		$this->cardTermStatus = $cardTermStatus;
	}
	/**
	 * カード有効ステータス設定
	 *
	 * @param string $cardInvalidStatus
	 */
	function setCardInvalidStatus($cardInvalidStatus) {
		$this->cardInvalidStatus = $cardInvalidStatus;
	}
	/**
	 * カードWEB参照ステータス設定
	 *
	 * @param string $cardWebInquiryStatus
	 */
	function setCardWebInquiryStatus($cardWebInquiryStatus) {
		$this->cardWebInquiryStatus = $cardWebInquiryStatus;
	}
	/**
	 * カード有効期限設定
	 *
	 * @param string $cardValidLimit
	 */
	function setCardValidLimit($cardValidLimit) {
		$this->cardValidLimit = $cardValidLimit;
	}
	/**
	 * 券種コード設定
	 *
	 * @param string $cardTypeCode
	 */
	function setCardTypeCode($cardTypeCode) {
		$this->cardTypeCode = $cardTypeCode;
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
		$str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .='&';
		$str .= 'Status=' . $this->encodeStr($this->getStatus());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'BeforeBalance=' . $this->encodeStr($this->getBeforeBalance());
		$str .='&';
		$str .= 'AfterBalance=' . $this->encodeStr($this->getAfterBalance());
		$str .='&';
		$str .= 'CardActivateStatus=' . $this->encodeStr($this->getCardActivateStatus());
		$str .='&';
		$str .= 'CardTermStatus=' . $this->encodeStr($this->getCardTermStatus());
		$str .='&';
		$str .= 'CardInvalidStatus=' . $this->encodeStr($this->getCardInvalidStatus());
		$str .='&';
		$str .= 'CardWebInquiryStatus=' . $this->encodeStr($this->getCardWebInquiryStatus());
		$str .='&';
		$str .= 'CardValidLimit=' . $this->encodeStr($this->getCardValidLimit());
		$str .='&';
		$str .= 'CardTypeCode=' . $this->encodeStr($this->getCardTypeCode());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
