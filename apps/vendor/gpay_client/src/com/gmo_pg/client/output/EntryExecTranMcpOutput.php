<?php
require_once 'com/gmo_pg/client/output/EntryTranMcpOutput.php';
require_once 'com/gmo_pg/client/output/ExecTranMcpOutput.php';
/**
 * <b>多通貨クレジットカード登録・決済一括実行  出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranMcpOutput {

	/**
	 * @var EntryTranMcpOutput 多通貨クレジットカード登録出力パラメータ
	 */
	var $entryTranMcpOutput;/*@var $entryTranMcpOutput EntryTranMcpOutput */

	/**
	 * @var ExecTranMcpOutput 多通貨クレジットカード実行出力パラメータ
	 */
	var $execTranMcpOutput;/*@var $execTranMcpOutput ExecTranMcpOutput */

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function EntryExecTranMcpOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params    入力パラメータ
	 */
	function __construct($params = null) {
		$this->entryTranMcpOutput = new EntryTranMcpOutput($params);
		$this->execTranMcpOutput = new ExecTranMcpOutput($params);
	}

	/**
	 * 多通貨クレジットカード登録出力パラメータ取得
	 * @return EntryTranMcpOutput 多通貨クレジットカード登録出力パラメータ
	 */
	function &getEntryTranMcpOutput() {
		return $this->entryTranMcpOutput;
	}

	/**
	 * 多通貨クレジットカード実行出力パラメータ取得
	 * @return ExecTranMcpOutput 多通貨クレジットカード実行出力パラメータ
	 */
	function &getExecTranMcpOutput() {
		return $this->execTranMcpOutput;
	}

	/**
	 * 多通貨クレジットカード登録出力パラメータ設定
	 *
	 * @param EntryTranMcpOutput  $entryTranMcpOutput 多通貨クレジットカード登録出力パラメータ
	 */
	function setEntryTranMcpOutput(&$entryTranMcpOutput) {
		$this->entryTranMcpOutput = $entryTranMcpOutput;
	}

	/**
	 * 多通貨クレジットカード決済実行出力パラメータ設定
	 *
	 * @param ExecTranMcpOutput $execTranMcpOutput 多通貨クレジットカード実行出力パラメータ
	 */
	function setExecTranMcpOutput(&$execTranMcpOutput) {
		$this->execTranMcpOutput = $execTranMcpOutput;
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->entryTranMcpOutput->getOrderID();

	}
	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->entryTranMcpOutput->getAccessID();

	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->entryTranMcpOutput->getAccessPass();

	}
	/**
	 * ACS呼出判定取得
	 * @return string ACS呼出判定
	 */
	function getACS() {
		return $this->execTranMcpOutput->getACS();

	}
	/**
	 * トークン取得
	 * @return string トークン
	 */
	function getToken() {
		return $this->execTranMcpOutput->getToken();

	}
	/**
	 * 支払手続き開始IFのURL取得
	 * @return string 支払手続き開始IFのURL
	 */
	function getStartURL() {
		return $this->execTranMcpOutput->getStartURL();

	}
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->execTranMcpOutput->getStatus();

	}
	/**
	 * MD5ハッシュ取得
	 * @return string MD5ハッシュ
	 */
	function getCheckString() {
		return $this->execTranMcpOutput->getCheckString();

	}
	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->execTranMcpOutput->getClientField1();

	}
	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->execTranMcpOutput->getClientField2();

	}
	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->execTranMcpOutput->getClientField3();

	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->entryTranMcpOutput->setOrderID($orderID);

	}
	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->entryTranMcpOutput->setAccessID($accessID);
		$this->execTranMcpOutput->setAccessID($accessID);

	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->entryTranMcpOutput->setAccessPass($accessPass);

	}
	/**
	 * ACS呼出判定設定
	 *
	 * @param string $aCS
	 */
	function setACS($aCS) {
		$this->execTranMcpOutput->setACS($aCS);

	}
	/**
	 * トークン設定
	 *
	 * @param string $token
	 */
	function setToken($token) {
		$this->execTranMcpOutput->setToken($token);

	}
	/**
	 * 支払手続き開始IFのURL設定
	 *
	 * @param string $startURL
	 */
	function setStartURL($startURL) {
		$this->execTranMcpOutput->setStartURL($startURL);

	}
	/**
	 * 現状態設定
	 *
	 * @param string $status
	 */
	function setStatus($status) {
		$this->execTranMcpOutput->setStatus($status);

	}
	/**
	 * MD5ハッシュ設定
	 *
	 * @param string $checkString
	 */
	function setCheckString($checkString) {
		$this->execTranMcpOutput->setCheckString($checkString);

	}
	/**
	 * 加盟店自由項目1設定
	 *
	 * @param string $clientField1
	 */
	function setClientField1($clientField1) {
		$this->execTranMcpOutput->setClientField1($clientField1);

	}
	/**
	 * 加盟店自由項目2設定
	 *
	 * @param string $clientField2
	 */
	function setClientField2($clientField2) {
		$this->execTranMcpOutput->setClientField2($clientField2);

	}
	/**
	 * 加盟店自由項目3設定
	 *
	 * @param string $clientField3
	 */
	function setClientField3($clientField3) {
		$this->execTranMcpOutput->setClientField3($clientField3);

	}

	/**
	 * 取引登録エラーリスト取得
	 * @return  array エラーリスト
	 */
	function &getEntryErrList() {
		return $this->entryTranMcpOutput->getErrList();
	}

	/**
	 * 決済実行エラーリスト取得
	 * @return array エラーリスト
	 */
	function &getExecErrList() {
		return $this->execTranMcpOutput->getErrList();
	}

	/**
	 * 取引登録エラー発生判定
	 * @return boolean 取引登録時エラー有無(true=エラー発生)
	 */
	function isEntryErrorOccurred() {
		$entryErrList =& $this->entryTranMcpOutput->getErrList();
		return 0 < count($entryErrList);
	}

	/**
	 * 決済実行エラー発生判定
	 * @return boolean 決済実行時エラー有無(true=エラー発生)
	 */
	function isExecErrorOccurred() {
		$execErrList =& $this->execTranMcpOutput->getErrList();
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
