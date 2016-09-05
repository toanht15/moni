<?php
require_once 'com/gmo_pg/client/output/EntryTranSbContinuanceOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranSbContinuanceOutput.php';
/**
 * <b>ソフトバンク継続登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranSbContinuanceOutput {

	/**
	 * @var EntryTranSbContinuanceOutput ソフトバンク継続登録出力パラメータ
	 */
	var $entryTranSbContinuanceOutput;/*@var $entryTranSbContinuanceOutput EntryTranSbContinuanceOutput */

	/**
	 * @var ExecTranSbContinuanceOutput ソフトバンク継続実行出力パラメータ
	 */
	var $execTranSbContinuanceOutput;/*@var $execTranSbContinuanceOutput ExecTranSbContinuanceOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranSbContinuanceOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranSbContinuanceOutput = new EntryTranSbContinuanceOutput($params);
		$this->execTranSbContinuanceOutput = new ExecTranSbContinuanceOutput($params);
	}

	/**
	 * ソフトバンク継続登録出力パラメータ取得
	 * @return EntryTranSbContinuanceOutput ソフトバンク継続登録出力パラメータ
	 */
	function &getEntryTranSbContinuanceOutput() {
		return $this->entryTranSbContinuanceOutput;
	}

	/**
	 * ソフトバンク継続実行出力パラメータ取得
	 * @return ExecTranSbContinuanceOutput ソフトバンク継続実行出力パラメータ
	 */
	function &getExecTranSbContinuanceOutput() {
		return $this->execTranSbContinuanceOutput;
	}

	/**
	 * ソフトバンク継続登録出力パラメータ設定
	 *
	 * @param EntryTranSbContinuanceOutput  $entryTranSbContinuanceOutput ソフトバンク継続登録出力パラメータ
	 */
	function setEntryTranSbContinuanceOutput(&$entryTranSbContinuanceOutput) {
		$this->entryTranSbContinuanceOutput = $entryTranSbContinuanceOutput;
	}

	/**
	 * ソフトバンク継続決済実行出力パラメータ設定
	 *
	 * @param ExecTranSbContinuanceOutput $execTranSbContinuanceOutput ソフトバンク継続実行出力パラメータ
	 */
	function setExecTranSbContinuanceOutput(&$execTranSbContinuanceOutput) {
		$this->execTranSbContinuanceOutput = $execTranSbContinuanceOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranSbContinuanceOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranSbContinuanceOutput->getAccessPass();

	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->execTranSbContinuanceOutput->getToken();

	}
	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranSbContinuanceOutput->getStartURL();

	}
	/**
	 * 支払開始期限日時取得
	 * @return string 支払開始期限日時
	 */
	function getStartLimitDate() {
		return $this->execTranSbContinuanceOutput->getStartLimitDate();

	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranSbContinuanceOutput->setAccessID($accessID);
		$this->execTranSbContinuanceOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranSbContinuanceOutput->setAccessPass($accessPass);

	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranSbContinuanceOutput->setToken($token);

	}
	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranSbContinuanceOutput->setStartURL($startURL);

	}
	/**
	 * 支払開始期限日時設定
	 *
	 * @param string $startLimitDate
	 */
	function setStartLimitDate($startLimitDate) {
		$this->execTranSbContinuanceOutput->setStartLimitDate($startLimitDate);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranSbContinuanceOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranSbContinuanceOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranSbContinuanceOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranSbContinuanceOutput->getErrList();
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
