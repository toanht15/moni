<?php
require_once 'com/gmo_pg/client/output/EntryTranLinepayOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranLinepayOutput.php';
/**
 * <b>LINE Pay登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranLinepayOutput {

	/**
	 * @var EntryTranLinepayOutput LINE Pay登録出力パラメータ
	 */
	var $entryTranLinepayOutput;/*@var $entryTranLinepayOutput EntryTranLinepayOutput */

	/**
	 * @var ExecTranLinepayOutput LINE Pay実行出力パラメータ
	 */
	var $execTranLinepayOutput;/*@var $execTranLinepayOutput ExecTranLinepayOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranLinepayOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranLinepayOutput = new EntryTranLinepayOutput($params);
		$this->execTranLinepayOutput = new ExecTranLinepayOutput($params);
	}

	/**
	 * LINE Pay登録出力パラメータ取得
	 * @return EntryTranLinepayOutput LINE Pay登録出力パラメータ
	 */
	function &getEntryTranLinepayOutput() {
		return $this->entryTranLinepayOutput;
	}

	/**
	 * LINE Pay実行出力パラメータ取得
	 * @return ExecTranLinepayOutput LINE Pay実行出力パラメータ
	 */
	function &getExecTranLinepayOutput() {
		return $this->execTranLinepayOutput;
	}

	/**
	 * LINE Pay登録出力パラメータ設定
	 *
	 * @param EntryTranLinepayOutput  $entryTranLinepayOutput LINE Pay登録出力パラメータ
	 */
	function setEntryTranLinepayOutput(&$entryTranLinepayOutput) {
		$this->entryTranLinepayOutput = $entryTranLinepayOutput;
	}

	/**
	 * LINE Pay決済実行出力パラメータ設定
	 *
	 * @param ExecTranLinepayOutput $execTranLinepayOutput LINE Pay実行出力パラメータ
	 */
	function setExecTranLinepayOutput(&$execTranLinepayOutput) {
		$this->execTranLinepayOutput = $execTranLinepayOutput;
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranLinepayOutput->getOrderID();

	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranLinepayOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranLinepayOutput->getAccessPass();

	}
	/**
	 * Startフラグ取得
	 * @return string Startフラグ
	 */
	function getStart() {
		return $this->execTranLinepayOutput->getStart();

	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->execTranLinepayOutput->getToken();

	}
	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranLinepayOutput->getStartURL();

	}
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->execTranLinepayOutput->getStatus();

	}
	/**
	 * 支払方法取得
	 * @return string 支払方法
	 */
	function getPayMethod() {
		return $this->execTranLinepayOutput->getPayMethod();

	}
	/**
	 * MD5ハッシュ取得
	 * @return string MD5ハッシュ
	 */
	function getCheckString() {
		return $this->execTranLinepayOutput->getCheckString();

	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranLinepayOutput->getClientField1();

	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranLinepayOutput->getClientField2();

	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranLinepayOutput->getClientField3();

	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranLinepayOutput->setOrderID($orderID);

	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranLinepayOutput->setAccessID($accessID);
		$this->execTranLinepayOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranLinepayOutput->setAccessPass($accessPass);

	}
	/**
	 * Startフラグ設定
	 *
	 * @param string $start
	 */
	function setStart($start) {
		$this->execTranLinepayOutput->setStart($start);

	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranLinepayOutput->setToken($token);

	}
	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranLinepayOutput->setStartURL($startURL);

	}
	/**
	 * 現状態設定
	 *
	 * @param string $status
	 */
	function setStatus($status) {
		$this->execTranLinepayOutput->setStatus($status);

	}
	/**
	 * 支払方法設定
	 *
	 * @param string $payMethod
	 */
	function setPayMethod($payMethod) {
		$this->execTranLinepayOutput->setPayMethod($payMethod);

	}
	/**
	 * MD5ハッシュ設定
	 *
	 * @param string $checkString
	 */
	function setCheckString($checkString) {
		$this->execTranLinepayOutput->setCheckString($checkString);

	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranLinepayOutput->setClientField1($clientField1);

	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranLinepayOutput->setClientField2($clientField2);

	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranLinepayOutput->setClientField3($clientField3);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranLinepayOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranLinepayOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranLinepayOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranLinepayOutput->getErrList();
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
