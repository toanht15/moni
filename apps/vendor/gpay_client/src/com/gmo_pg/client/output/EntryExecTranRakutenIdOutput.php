<?php
require_once 'com/gmo_pg/client/output/EntryTranRakutenIdOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranRakutenIdOutput.php';
/**
 * <b>楽天ID登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRakutenIdOutput {

	/**
	 * @var EntryTranRakutenIdOutput 楽天ID登録出力パラメータ
	 */
	var $entryTranRakutenIdOutput;/*@var $entryTranRakutenIdOutput EntryTranRakutenIdOutput */

	/**
	 * @var ExecTranRakutenIdOutput 楽天ID実行出力パラメータ
	 */
	var $execTranRakutenIdOutput;/*@var $execTranRakutenIdOutput ExecTranRakutenIdOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranRakutenIdOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranRakutenIdOutput = new EntryTranRakutenIdOutput($params);
		$this->execTranRakutenIdOutput = new ExecTranRakutenIdOutput($params);
	}

	/**
	 * 楽天ID登録出力パラメータ取得
	 * @return EntryTranRakutenIdOutput 楽天ID登録出力パラメータ
	 */
	function &getEntryTranRakutenIdOutput() {
		return $this->entryTranRakutenIdOutput;
	}

	/**
	 * 楽天ID実行出力パラメータ取得
	 * @return ExecTranRakutenIdOutput 楽天ID実行出力パラメータ
	 */
	function &getExecTranRakutenIdOutput() {
		return $this->execTranRakutenIdOutput;
	}

	/**
	 * 楽天ID登録出力パラメータ設定
	 *
	 * @param EntryTranRakutenIdOutput  $entryTranRakutenIdOutput 楽天ID登録出力パラメータ
	 */
	function setEntryTranRakutenIdOutput(&$entryTranRakutenIdOutput) {
		$this->entryTranRakutenIdOutput = $entryTranRakutenIdOutput;
	}

	/**
	 * 楽天ID決済実行出力パラメータ設定
	 *
	 * @param ExecTranRakutenIdOutput $execTranRakutenIdOutput 楽天ID実行出力パラメータ
	 */
	function setExecTranRakutenIdOutput(&$execTranRakutenIdOutput) {
		$this->execTranRakutenIdOutput = $execTranRakutenIdOutput;
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranRakutenIdOutput->getOrderID();

	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranRakutenIdOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranRakutenIdOutput->getAccessPass();

	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->execTranRakutenIdOutput->getToken();

	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranRakutenIdOutput->setOrderID($orderID);

	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranRakutenIdOutput->setAccessID($accessID);
		$this->execTranRakutenIdOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranRakutenIdOutput->setAccessPass($accessPass);

	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranRakutenIdOutput->setToken($token);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranRakutenIdOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranRakutenIdOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranRakutenIdOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranRakutenIdOutput->getErrList();
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
