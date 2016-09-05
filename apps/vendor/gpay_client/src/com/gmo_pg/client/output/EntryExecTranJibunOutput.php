<?php
require_once 'com/gmo_pg/client/output/EntryTranJibunOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranJibunOutput.php';
/**
 * <b>じぶん銀行決済登録・決済一括実行  出力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/31
 */
class EntryExecTranJibunOutput {

	/**
	 * @var EntryTranJibunOutput じぶん銀行決済登録出力パラメータ
	 */
	var $entryTranJibunOutput;/*@var $entryTranJibunOutput EntryTranJibunOutput */

	/**
	 * @var ExecTranJibunOutput じぶん銀行決済実行出力パラメータ
	 */
	var $execTranJibunOutput;/*@var $execTranJibunOutput ExecTranJibunOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranJibunOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranJibunOutput = new EntryTranJibunOutput($params);
		$this->execTranJibunOutput = new ExecTranJibunOutput($params);
	}

	/**
	 * じぶん銀行決済登録出力パラメータ取得
	 * @return EntryTranJibunOutput じぶん銀行決済登録出力パラメータ
	 */
	function &getEntryTranJibunOutput() {
		return $this->entryTranJibunOutput;
	}

	/**
	 * じぶん銀行決済実行出力パラメータ取得
	 * @return ExecTranJibunOutput じぶん銀行決済実行出力パラメータ
	 */
	function &getExecTranJibunOutput() {
		return $this->execTranJibunOutput;
	}

	/**
	 * じぶん銀行決済登録出力パラメータ設定
	 *
	 * @param EntryTranJibunOutput  $entryTranJibunOutput じぶん銀行決済登録出力パラメータ
	 */
	function setEntryTranJibunOutput(&$entryTranJibunOutput) {
		$this->entryTranJibunOutput = $entryTranJibunOutput;
	}

	/**
	 * じぶん銀行決済決済実行出力パラメータ設定
	 *
	 * @param ExecTranJibunOutput $execTranJibunOutput じぶん銀行決済実行出力パラメータ
	 */
	function setExecTranJibunOutput(&$execTranJibunOutput) {
		$this->execTranJibunOutput = $execTranJibunOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranJibunOutput->getAccessID();
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranJibunOutput->getAccessPass();
	}

	/**
	 * 決済トークン取得
	 * @return string 決済トークン
	 */
	function getToken() {
		return $this->execTranJibunOutput->getToken();
	}

	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranJibunOutput->getStartURL();
	}

	/**
	 * 支払開始期限日時取得
	 * @return string 支払開始期限日時
	 */
	function getStartLimitDate() {
		return $this->execTranJibunOutput->getStartLimitDate();
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranJibunOutput->setAccessID($accessID);
		$this->execTranJibunOutput->setAccessID($accessID);
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranJibunOutput->setAccessPass($accessPass);
	}

	/**
	 * 決済トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranJibunOutput->setToken($token);
	}

	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranJibunOutput->setStartURL($startURL);
	}

	/**
	 * 支払開始期限日時設定
	 *
	 * @param string $startLimitDate
	 */
	function setStartLimitDate($startLimitDate) {
		$this->execTranJibunOutput->setStartLimitDate($startLimitDate);
	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranJibunOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranJibunOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranJibunOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranJibunOutput->getErrList();
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
