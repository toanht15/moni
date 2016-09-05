<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>JCBプリカ残高照会　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class JcbPrecaBalanceInquiryOutput extends BaseOutput {

	/**
	 * @var string 取引ID
	 */
	var $cardNo;
	/**
	 * @var string 取引パスワード
	 */
	var $balance;
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
	function JcbPrecaBalanceInquiryOutput($params = null) {
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
		$this->setCardNo($params->get('CardNo'));
		$this->setBalance($params->get('Balance'));
		$this->setCardActivateStatus($params->get('CardActivateStatus'));
		$this->setCardTermStatus($params->get('CardTermStatus'));
		$this->setCardInvalidStatus($params->get('CardInvalidStatus'));
		$this->setCardWebInquiryStatus($params->get('CardWebInquiryStatus'));
		$this->setCardValidLimit($params->get('CardValidLimit'));
		$this->setCardTypeCode($params->get('CardTypeCode'));

	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getCardNo() {
		return $this->cardNo;
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getBalance() {
		return $this->balance;
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
	 * 取引ID設定
	 *
	 * @param string $cardNo
	 */
	function setCardNo($cardNo) {
		$this->cardNo = $cardNo;
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $balance
	 */
	function setBalance($balance) {
		$this->balance = $balance;
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
		$str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
		$str .='&';
		$str .= 'Balance=' . $this->encodeStr($this->getBalance());
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
