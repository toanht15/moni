<?php
require_once 'com/gmo_pg/client/output/EntryTranNetcashOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranNetcashOutput.php';
/**
 * <b>NET CASH登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranNetcashOutput {

	/**
	 * @var EntryTranNetcashOutput NET CASH登録出力パラメータ
	 */
	var $entryTranNetcashOutput;/*@var $entryTranNetcashOutput EntryTranNetcashOutput */

	/**
	 * @var ExecTranNetcashOutput NET CASH実行出力パラメータ
	 */
	var $execTranNetcashOutput;/*@var $execTranNetcashOutput ExecTranNetcashOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranNetcashOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranNetcashOutput = new EntryTranNetcashOutput($params);
		$this->execTranNetcashOutput = new ExecTranNetcashOutput($params);
	}

	/**
	 * NET CASH登録出力パラメータ取得
	 * @return EntryTranNetcashOutput NET CASH登録出力パラメータ
	 */
	function &getEntryTranNetcashOutput() {
		return $this->entryTranNetcashOutput;
	}

	/**
	 * NET CASH実行出力パラメータ取得
	 * @return ExecTranNetcashOutput NET CASH実行出力パラメータ
	 */
	function &getExecTranNetcashOutput() {
		return $this->execTranNetcashOutput;
	}

	/**
	 * NET CASH登録出力パラメータ設定
	 *
	 * @param EntryTranNetcashOutput  $entryTranNetcashOutput NET CASH登録出力パラメータ
	 */
	function setEntryTranNetcashOutput(&$entryTranNetcashOutput) {
		$this->entryTranNetcashOutput = $entryTranNetcashOutput;
	}

	/**
	 * NET CASH決済実行出力パラメータ設定
	 *
	 * @param ExecTranNetcashOutput $execTranNetcashOutput NET CASH実行出力パラメータ
	 */
	function setExecTranNetcashOutput(&$execTranNetcashOutput) {
		$this->execTranNetcashOutput = $execTranNetcashOutput;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranNetcashOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranNetcashOutput->getAccessPass();

	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranNetcashOutput->setAccessID($accessID);
		$this->execTranNetcashOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranNetcashOutput->setAccessPass($accessPass);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranNetcashOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranNetcashOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranNetcashOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranNetcashOutput->getErrList();
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
