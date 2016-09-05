<?php
require_once 'com/gmo_pg/client/output/EntryTranSbOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranSbOutput.php';
/**
 * <b>ソフトバンクケータイ支払い登録・決済一括実行  出力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/18
 */
class EntryExecTranSbOutput {

	/**
	 * @var EntryTranSbOutput ソフトバンクケータイ支払い登録出力パラメータ
	 */
	var $entryTranSbOutput;/*@var $entryTranSbOutput EntryTranSbOutput */

	/**
	 * @var ExecTranSbOutput ソフトバンクケータイ支払い実行出力パラメータ
	 */
	var $execTranSbOutput;/*@var $execTranSbOutput ExecTranSbOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranSbOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranSbOutput = new EntryTranSbOutput($params);
		$this->execTranSbOutput = new ExecTranSbOutput($params);
	}

	/**
	 * ソフトバンクケータイ支払い登録出力パラメータ取得
	 * @return EntryTranSbOutput ソフトバンクケータイ支払い登録出力パラメータ
	 */
	function &getEntryTranSbOutput() {
		return $this->entryTranSbOutput;
	}

	/**
	 * ソフトバンクケータイ支払い実行出力パラメータ取得
	 * @return ExecTranSbOutput ソフトバンクケータイ支払い実行出力パラメータ
	 */
	function &getExecTranSbOutput() {
		return $this->execTranSbOutput;
	}

	/**
	 * ソフトバンクケータイ支払い登録出力パラメータ設定
	 *
	 * @param EntryTranSbOutput  $entryTranSbOutput ソフトバンクケータイ支払い登録出力パラメータ
	 */
	function setEntryTranSbOutput(&$entryTranSbOutput) {
		$this->entryTranSbOutput = $entryTranSbOutput;
	}

	/**
	 * ソフトバンクケータイ支払い決済実行出力パラメータ設定
	 *
	 * @param ExecTranSbOutput $execTranSbOutput ソフトバンクケータイ支払い実行出力パラメータ
	 */
	function setExecTranSbOutput(&$execTranSbOutput) {
		$this->execTranSbOutput = $execTranSbOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranSbOutput->getAccessID();
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranSbOutput->getAccessPass();
	}

	/**
	 * 決済トークン取得
	 * @return string 決済トークン
	 */
	function getToken() {
		return $this->execTranSbOutput->getToken();
	}

	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranSbOutput->getStartURL();
	}

	/**
	 * 支払開始期限日時取得
	 * @return string 支払開始期限日時
	 */
	function getStartLimitDate() {
		return $this->execTranSbOutput->getStartLimitDate();
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranSbOutput->setAccessID($accessID);
		$this->execTranSbOutput->setAccessID($accessID);
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranSbOutput->setAccessPass($accessPass);
	}

	/**
	 * 決済トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranSbOutput->setToken($token);
	}

	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranSbOutput->setStartURL($startURL);
	}

	/**
	 * 支払開始期限日時設定
	 *
	 * @param string $startLimitDate
	 */
	function setStartLimitDate($startLimitDate) {
		$this->execTranSbOutput->setStartLimitDate($startLimitDate);
	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranSbOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranSbOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranSbOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranSbOutput->getErrList();
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
