<?php
require_once 'com/gmo_pg/client/output/EntryExecTranSbOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranSb.php';
require_once 'com/gmo_pg/client/tran/ExecTranSb.php';
/**
 * <b>ソフトバンクケータイ支払い登録・決済一括実行　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/18
 */
class EntryExecTranSb {
	/**
	 * @var Log ログ
	 */
	var $log;

	/**
	 * @var GPayException 例外
	 */
	var $exception;

	/**
	 * コンストラクタ
	 */
	function EntryExecTranSb() {
		$this->__construct();
	}

	/**
	 * コンストラクタ
	 */
	function __construct() {
		$this->log = new Log(get_class($this));
	}

	/**
	 * 例外の発生を判定する
	 *
	 * @param mixed $target    判定対象
	 */
	function errorTrap(&$target) {
		if (is_null($target->exception)) {
			return false;
		}
		$this->exception = $target->exception;
		return true;
	}

	/**
	 * 例外の発生を判定する
	 *
	 * @return  boolean 判定結果(true=エラーアリ)
	 */
	function isExceptionOccured() {
		return false == is_null($this->exception);
	}

	/**
	 * 例外を返す
	 *
	 * @return  GPayException 例外
	 */
	function &getException() {
		return $this->exception;
	}

	/**
	 * ソフトバンクケータイ支払い登録・決済を実行する
	 *
	 * @param EntryExecTranSbInput $input    ソフトバンクケータイ支払い登録・決済入力パラメータ
	 * @return  EntryExecTranSbOutput ソフトバンクケータイ支払い登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// ソフトバンクケータイ支払い取引登録入力パラメータを取得
		$entryTranSbInput =& $input->getEntryTranSbInput();
		// ソフトバンクケータイ支払い決済実行入力パラメータを取得
		$execTranSbInput =& $input->getExecTranSbInput();

		// ソフトバンクケータイ支払い登録・決済出力パラメータを生成
		$output = new EntryExecTranSbOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranSbInput->getAccessId();
		$accessPass = $execTranSbInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// ソフトバンクケータイ支払い取引登録を実行
			$this->log->debug("ソフトバンクケータイ支払い取引登録実行");
			$entryTranSb = new EntryTranSb();
			$entryTranSbOutput = $entryTranSb->exec($entryTranSbInput);

			if ($this->errorTrap($entryTranSb)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranSbOutput->getAccessId();
			$accessPass = $entryTranSbOutput->getAccessPass();
			$execTranSbInput->setAccessId($accessId);
			$execTranSbInput->setAccessPass($accessPass);

			$output->setEntryTranSbOutput($entryTranSbOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranSb = new ExecTranSb();
		$execTranSbOutput = $execTranSb->exec($execTranSbInput);

		$output->setExecTranSbOutput($execTranSbOutput);

		$this->errorTrap($execTranSb);

		return $output;
	}


}
?>
