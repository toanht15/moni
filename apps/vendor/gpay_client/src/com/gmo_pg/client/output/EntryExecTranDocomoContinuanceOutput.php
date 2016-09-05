<?php
require_once 'com/gmo_pg/client/output/EntryTranDocomoContinuanceOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranDocomoContinuanceOutput.php';
/**
 * <b>ドコモ継続決済　登録・決済一括実行  出力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/08/21
 */
class EntryExecTranDocomoContinuanceOutput {

	/**
	 * @var EntryTranDocomoContinuanceOutput ドコモ継続決済登録出力パラメータ
	 */
	var $entryTranDocomoContinuanceOutput;/*@var $entryTranDocomoContinuanceOutput EntryTranDocomoContinuanceOutput */

	/**
	 * @var ExecTranDocomoContinuanceOutput ドコモ継続決済実行出力パラメータ
	 */
	var $execTranDocomoContinuanceOutput;/*@var $execTranDocomoContinuanceOutput ExecTranDocomoContinuanceOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranDocomoContinuanceOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranDocomoContinuanceOutput = new EntryTranDocomoContinuanceOutput($params);
		$this->execTranDocomoContinuanceOutput = new ExecTranDocomoContinuanceOutput($params);
	}

	/**
	 * ドコモ継続決済登録出力パラメータ取得
	 * @return EntryTranDocomoContinuanceOutput ドコモ継続決済登録出力パラメータ
	 */
	function &getEntryTranDocomoContinuanceOutput() {
		return $this->entryTranDocomoContinuanceOutput;
	}

	/**
	 * ドコモ継続決済実行出力パラメータ取得
	 * @return ExecTranDocomoContinuanceOutput ドコモ継続決済実行出力パラメータ
	 */
	function &getExecTranDocomoContinuanceOutput() {
		return $this->execTranDocomoContinuanceOutput;
	}

	/**
	 * ドコモ継続決済登録出力パラメータ設定
	 *
	 * @param EntryTranDocomoContinuanceOutput  $entryTranDocomoContinuanceOutput ドコモ継続決済登録出力パラメータ
	 */
	function setEntryTranDocomoContinuanceOutput(&$entryTranDocomoContinuanceOutput) {
		$this->entryTranDocomoContinuanceOutput = $entryTranDocomoContinuanceOutput;
	}

	/**
	 * ドコモ継続決済決済実行出力パラメータ設定
	 *
	 * @param ExecTranDocomoContinuanceOutput $execTranDocomoContinuanceOutput ドコモ継続決済実行出力パラメータ
	 */
	function setExecTranDocomoContinuanceOutput(&$execTranDocomoContinuanceOutput) {
		$this->execTranDocomoContinuanceOutput = $execTranDocomoContinuanceOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranDocomoContinuanceOutput->getAccessID();
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranDocomoContinuanceOutput->getAccessPass();
	}

	/**
	 * 決済トークン取得
	 * @return string 決済トークン
	 */
	function getToken() {
		return $this->execTranDocomoContinuanceOutput->getToken();
	}

	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranDocomoContinuanceOutput->getStartURL();
	}

	/**
	 * 支払開始期限日時取得
	 * @return string 支払開始期限日時
	 */
	function getStartLimitDate() {
		return $this->execTranDocomoContinuanceOutput->getStartLimitDate();
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranDocomoContinuanceOutput->setAccessID($accessID);
		$this->execTranDocomoContinuanceOutput->setAccessID($accessID);
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranDocomoContinuanceOutput->setAccessPass($accessPass);
	}

	/**
	 * 決済トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranDocomoContinuanceOutput->setToken($token);
	}

	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranDocomoContinuanceOutput->setStartURL($startURL);
	}

	/**
	 * 支払開始期限日時設定
	 *
	 * @param string $startLimitDate
	 */
	function setStartLimitDate($startLimitDate) {
		$this->execTranDocomoContinuanceOutput->setStartLimitDate($startLimitDate);
	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranDocomoContinuanceOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranDocomoContinuanceOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranDocomoContinuanceOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranDocomoContinuanceOutput->getErrList();
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
