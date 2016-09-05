<?php
require_once 'com/gmo_pg/client/output/EntryTranAuContinuanceOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranAuContinuanceOutput.php';
/**
 * <b>auかんたん決済継続課金継続課金登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php 
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/02/15
 */
class EntryExecTranAuContinuanceOutput {

	/**
	 * @var EntryTranAuContinuanceOutput auかんたん決済継続課金登録出力パラメータ
	 */
	var $entryTranAuContinuanceOutput;/*@var $entryTranAuContinuanceOutput EntryTranAuContinuanceOutput */

	/**
	 * @var ExecTranAuContinuanceOutput auかんたん決済継続課金実行出力パラメータ
	 */
	var $execTranAuContinuanceOutput;/*@var $execTranAuContinuanceOutput ExecTranAuContinuanceOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranAuContinuanceOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranAuContinuanceOutput = new EntryTranAuContinuanceOutput($params);
		$this->execTranAuContinuanceOutput = new ExecTranAuContinuanceOutput($params);
	}

	/**
	 * auかんたん決済継続課金登録出力パラメータ取得
	 * @return EntryTranAuContinuanceOutput auかんたん決済継続課金登録出力パラメータ
	 */
	function &getEntryTranAuContinuanceOutput() {
		return $this->entryTranAuContinuanceOutput;
	}

	/**
	 * auかんたん決済継続課金実行出力パラメータ取得
	 * @return ExecTranAuContinuanceOutput auかんたん決済継続課金実行出力パラメータ
	 */
	function &getExecTranAuContinuanceOutput() {
		return $this->execTranAuContinuanceOutput;
	}

	/**
	 * auかんたん決済継続課金登録出力パラメータ設定
	 *
	 * @param EntryTranAuContinuanceOutput  $entryTranAuContinuanceOutput auかんたん決済継続課金登録出力パラメータ
	 */
	function setEntryTranAuContinuanceOutput(&$entryTranAuContinuanceOutput) {
		$this->entryTranAuContinuanceOutput = $entryTranAuContinuanceOutput;
	}

	/**
	 * auかんたん決済継続課金決済実行出力パラメータ設定
	 *
	 * @param ExecTranAuContinuanceOutput $execTranAuContinuanceOutput auかんたん決済継続課金実行出力パラメータ
	 */
	function setExecTranAuContinuanceOutput(&$execTranAuContinuanceOutput) {
		$this->execTranAuContinuanceOutput = $execTranAuContinuanceOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranAuContinuanceOutput->getAccessID();
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranAuContinuanceOutput->getAccessPass();
	}

	/**
	 * 決済トークン取得
	 * @return string 決済トークン
	 */
	function getToken() {
		return $this->execTranAuContinuanceOutput->getToken();
	}

	/**
	 * 呼び出しURL取得
	 * @return string 呼び出しURL
	 */
	function getStartURL() {
		return $this->execTranAuContinuanceOutput->getStartURL();
	}

	/**
	 * 該当トークンの有効期限 YYYYMMDDHHMM取得
	 * @return string 該当トークンの有効期限 YYYYMMDDHHMM
	 */
	function getStartLimitDate() {
		return $this->execTranAuContinuanceOutput->getStartLimitDate();
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranAuContinuanceOutput->setAccessID($accessID);
		$this->execTranAuContinuanceOutput->setAccessID($accessID);
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranAuContinuanceOutput->setAccessPass($accessPass);
	}

	/**
	 * 決済トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranAuContinuanceOutput->setToken($token);
	}

	/**
	 * 呼び出しURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranAuContinuanceOutput->setStartURL($startURL);
	}

	/**
	 * 該当トークンの有効期限 YYYYMMDDHHMM設定
	 *
	 * @param string $startLimitDate
	 */
	function setStartLimitDate($startLimitDate) {
		$this->execTranAuContinuanceOutput->setStartLimitDate($startLimitDate);
	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranAuContinuanceOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranAuContinuanceOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranAuContinuanceOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranAuContinuanceOutput->getErrList();
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