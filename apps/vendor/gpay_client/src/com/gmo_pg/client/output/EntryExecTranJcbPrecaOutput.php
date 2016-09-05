<?php
require_once 'com/gmo_pg/client/output/EntryTranJcbPrecaOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranJcbPrecaOutput.php';
/**
 * <b>JCBプリカ登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranJcbPrecaOutput {

	/**
	 * @var EntryTranJcbPrecaOutput JCBプリカ登録出力パラメータ
	 */
	var $entryTranJcbPrecaOutput;/*@var $entryTranJcbPrecaOutput EntryTranJcbPrecaOutput */

	/**
	 * @var ExecTranJcbPrecaOutput JCBプリカ実行出力パラメータ
	 */
	var $execTranJcbPrecaOutput;/*@var $execTranJcbPrecaOutput ExecTranJcbPrecaOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranJcbPrecaOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranJcbPrecaOutput = new EntryTranJcbPrecaOutput($params);
		$this->execTranJcbPrecaOutput = new ExecTranJcbPrecaOutput($params);
	}

	/**
	 * JCBプリカ登録出力パラメータ取得
	 * @return EntryTranJcbPrecaOutput JCBプリカ登録出力パラメータ
	 */
	function &getEntryTranJcbPrecaOutput() {
		return $this->entryTranJcbPrecaOutput;
	}

	/**
	 * JCBプリカ実行出力パラメータ取得
	 * @return ExecTranJcbPrecaOutput JCBプリカ実行出力パラメータ
	 */
	function &getExecTranJcbPrecaOutput() {
		return $this->execTranJcbPrecaOutput;
	}

	/**
	 * JCBプリカ登録出力パラメータ設定
	 *
	 * @param EntryTranJcbPrecaOutput  $entryTranJcbPrecaOutput JCBプリカ登録出力パラメータ
	 */
	function setEntryTranJcbPrecaOutput(&$entryTranJcbPrecaOutput) {
		$this->entryTranJcbPrecaOutput = $entryTranJcbPrecaOutput;
	}

	/**
	 * JCBプリカ決済実行出力パラメータ設定
	 *
	 * @param ExecTranJcbPrecaOutput $execTranJcbPrecaOutput JCBプリカ実行出力パラメータ
	 */
	function setExecTranJcbPrecaOutput(&$execTranJcbPrecaOutput) {
		$this->execTranJcbPrecaOutput = $execTranJcbPrecaOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranJcbPrecaOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranJcbPrecaOutput->getAccessPass();

	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->execTranJcbPrecaOutput->getOrderID();

	}
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->execTranJcbPrecaOutput->getStatus();

	}
	/**
	 * 利用金額取得
	 * @return integer 利用金額
	 */
	function getAmount() {
		return $this->execTranJcbPrecaOutput->getAmount();

	}
	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->execTranJcbPrecaOutput->getTax();

	}
	/**
	 * 利用前残高取得
	 * @return integer 利用前残高
	 */
	function getBeforeBalance() {
		return $this->execTranJcbPrecaOutput->getBeforeBalance();

	}
	/**
	 * 利用後残高取得
	 * @return integer 利用後残高
	 */
	function getAfterBalance() {
		return $this->execTranJcbPrecaOutput->getAfterBalance();

	}
	/**
	 * カードアクティベートステータス取得
	 * @return string カードアクティベートステータス
	 */
	function getCardActivateStatus() {
		return $this->execTranJcbPrecaOutput->getCardActivateStatus();

	}
	/**
	 * カード有効期限ステータス取得
	 * @return string カード有効期限ステータス
	 */
	function getCardTermStatus() {
		return $this->execTranJcbPrecaOutput->getCardTermStatus();

	}
	/**
	 * カード有効ステータス取得
	 * @return string カード有効ステータス
	 */
	function getCardInvalidStatus() {
		return $this->execTranJcbPrecaOutput->getCardInvalidStatus();

	}
	/**
	 * カードWEB参照ステータス取得
	 * @return string カードWEB参照ステータス
	 */
	function getCardWebInquiryStatus() {
		return $this->execTranJcbPrecaOutput->getCardWebInquiryStatus();

	}
	/**
	 * カード有効期限取得
	 * @return string カード有効期限
	 */
	function getCardValidLimit() {
		return $this->execTranJcbPrecaOutput->getCardValidLimit();

	}
	/**
	 * 券種コード取得
	 * @return string 券種コード
	 */
	function getCardTypeCode() {
		return $this->execTranJcbPrecaOutput->getCardTypeCode();

	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranJcbPrecaOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranJcbPrecaOutput->setAccessPass($accessPass);

	}
	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->execTranJcbPrecaOutput->setOrderID($orderID);

	}
	/**
	 * 現状態設定
	 *
	 * @param string $status
	 */
	function setStatus($status) {
		$this->execTranJcbPrecaOutput->setStatus($status);

	}
	/**
	 * 利用金額設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->execTranJcbPrecaOutput->setAmount($amount);

	}
	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->execTranJcbPrecaOutput->setTax($tax);

	}
	/**
	 * 利用前残高設定
	 *
	 * @param integer $beforeBalance
	 */
	function setBeforeBalance($beforeBalance) {
		$this->execTranJcbPrecaOutput->setBeforeBalance($beforeBalance);

	}
	/**
	 * 利用後残高設定
	 *
	 * @param integer $afterBalance
	 */
	function setAfterBalance($afterBalance) {
		$this->execTranJcbPrecaOutput->setAfterBalance($afterBalance);

	}
	/**
	 * カードアクティベートステータス設定
	 *
	 * @param string $cardActivateStatus
	 */
	function setCardActivateStatus($cardActivateStatus) {
		$this->execTranJcbPrecaOutput->setCardActivateStatus($cardActivateStatus);

	}
	/**
	 * カード有効期限ステータス設定
	 *
	 * @param string $cardTermStatus
	 */
	function setCardTermStatus($cardTermStatus) {
		$this->execTranJcbPrecaOutput->setCardTermStatus($cardTermStatus);

	}
	/**
	 * カード有効ステータス設定
	 *
	 * @param string $cardInvalidStatus
	 */
	function setCardInvalidStatus($cardInvalidStatus) {
		$this->execTranJcbPrecaOutput->setCardInvalidStatus($cardInvalidStatus);

	}
	/**
	 * カードWEB参照ステータス設定
	 *
	 * @param string $cardWebInquiryStatus
	 */
	function setCardWebInquiryStatus($cardWebInquiryStatus) {
		$this->execTranJcbPrecaOutput->setCardWebInquiryStatus($cardWebInquiryStatus);

	}
	/**
	 * カード有効期限設定
	 *
	 * @param string $cardValidLimit
	 */
	function setCardValidLimit($cardValidLimit) {
		$this->execTranJcbPrecaOutput->setCardValidLimit($cardValidLimit);

	}
	/**
	 * 券種コード設定
	 *
	 * @param string $cardTypeCode
	 */
	function setCardTypeCode($cardTypeCode) {
		$this->execTranJcbPrecaOutput->setCardTypeCode($cardTypeCode);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranJcbPrecaOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranJcbPrecaOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranJcbPrecaOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranJcbPrecaOutput->getErrList();
		return 0 < count($execErrList);
	}

	/**
	 * エラー発生判定
	 * @return boolean エラー発生有無(true=エラー発生)
	 */
	function isErrorOccurred() {
		return $this->isEntryErrorOccurred() || $this->isExecErrorOccurred();
	}

}
?>
