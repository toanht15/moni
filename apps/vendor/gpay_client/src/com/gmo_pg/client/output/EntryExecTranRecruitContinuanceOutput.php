<?php
require_once 'com/gmo_pg/client/output/EntryTranRecruitContinuanceOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranRecruitContinuanceOutput.php';
/**
 * <b>リクルートかんたん支払い継続課金登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRecruitContinuanceOutput {

	/**
	 * @var EntryTranRecruitContinuanceOutput リクルートかんたん支払い継続課金登録出力パラメータ
	 */
	var $entryTranRecruitContinuanceOutput;/*@var $entryTranRecruitContinuanceOutput EntryTranRecruitContinuanceOutput */

	/**
	 * @var ExecTranRecruitContinuanceOutput リクルートかんたん支払い継続課金実行出力パラメータ
	 */
	var $execTranRecruitContinuanceOutput;/*@var $execTranRecruitContinuanceOutput ExecTranRecruitContinuanceOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranRecruitContinuanceOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranRecruitContinuanceOutput = new EntryTranRecruitContinuanceOutput($params);
		$this->execTranRecruitContinuanceOutput = new ExecTranRecruitContinuanceOutput($params);
	}

	/**
	 * リクルートかんたん支払い継続課金登録出力パラメータ取得
	 * @return EntryTranRecruitContinuanceOutput リクルートかんたん支払い継続課金登録出力パラメータ
	 */
	function &getEntryTranRecruitContinuanceOutput() {
		return $this->entryTranRecruitContinuanceOutput;
	}

	/**
	 * リクルートかんたん支払い継続課金実行出力パラメータ取得
	 * @return ExecTranRecruitContinuanceOutput リクルートかんたん支払い継続課金実行出力パラメータ
	 */
	function &getExecTranRecruitContinuanceOutput() {
		return $this->execTranRecruitContinuanceOutput;
	}

	/**
	 * リクルートかんたん支払い継続課金登録出力パラメータ設定
	 *
	 * @param EntryTranRecruitContinuanceOutput  $entryTranRecruitContinuanceOutput リクルートかんたん支払い継続課金登録出力パラメータ
	 */
	function setEntryTranRecruitContinuanceOutput(&$entryTranRecruitContinuanceOutput) {
		$this->entryTranRecruitContinuanceOutput = $entryTranRecruitContinuanceOutput;
	}

	/**
	 * リクルートかんたん支払い継続課金決済実行出力パラメータ設定
	 *
	 * @param ExecTranRecruitContinuanceOutput $execTranRecruitContinuanceOutput リクルートかんたん支払い継続課金実行出力パラメータ
	 */
	function setExecTranRecruitContinuanceOutput(&$execTranRecruitContinuanceOutput) {
		$this->execTranRecruitContinuanceOutput = $execTranRecruitContinuanceOutput;
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranRecruitContinuanceOutput->getOrderID();

	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranRecruitContinuanceOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranRecruitContinuanceOutput->getAccessPass();

	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->execTranRecruitContinuanceOutput->getToken();

	}
	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranRecruitContinuanceOutput->getStartURL();

	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranRecruitContinuanceOutput->setOrderID($orderID);

	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranRecruitContinuanceOutput->setAccessID($accessID);
		$this->execTranRecruitContinuanceOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranRecruitContinuanceOutput->setAccessPass($accessPass);

	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranRecruitContinuanceOutput->setToken($token);

	}
	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranRecruitContinuanceOutput->setStartURL($startURL);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranRecruitContinuanceOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranRecruitContinuanceOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranRecruitContinuanceOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranRecruitContinuanceOutput->getErrList();
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
