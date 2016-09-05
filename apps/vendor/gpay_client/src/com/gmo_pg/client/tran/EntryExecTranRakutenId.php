<?php
require_once 'com/gmo_pg/client/output/EntryExecTranRakutenIdOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranRakutenId.php';
require_once 'com/gmo_pg/client/tran/ExecTranRakutenId.php';
/**
 * <b>楽天ID登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRakutenId {
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
	function EntryExecTranRakutenId() {
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
	 * 楽天ID登録・決済を実行する
	 *
	 * @param EntryExecTranRakutenIdInput $input    楽天ID登録・決済入力パラメータ
	 * @return  EntryExecTranRakutenIdOutput 楽天ID登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// 楽天ID取引登録入力パラメータを取得
		$entryTranRakutenIdInput =& $input->getEntryTranRakutenIdInput();
		// 楽天ID決済実行入力パラメータを取得
		$execTranRakutenIdInput =& $input->getExecTranRakutenIdInput();

		// 楽天ID登録・決済出力パラメータを生成
		$output = new EntryExecTranRakutenIdOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranRakutenIdInput->getAccessId();
		$accessPass = $execTranRakutenIdInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// 楽天ID取引登録を実行
			$this->log->debug("楽天ID取引登録実行");
			$entryTranRakutenId = new EntryTranRakutenId();
			$entryTranRakutenIdOutput = $entryTranRakutenId->exec($entryTranRakutenIdInput);

			if ($this->errorTrap($entryTranRakutenId)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranRakutenIdOutput->getAccessId();
			$accessPass = $entryTranRakutenIdOutput->getAccessPass();
			$execTranRakutenIdInput->setAccessId($accessId);
			$execTranRakutenIdInput->setAccessPass($accessPass);

			$output->setEntryTranRakutenIdOutput($entryTranRakutenIdOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranRakutenId = new ExecTranRakutenId();
		$execTranRakutenIdOutput = $execTranRakutenId->exec($execTranRakutenIdInput);

		$output->setExecTranRakutenIdOutput($execTranRakutenIdOutput);

		$this->errorTrap($execTranRakutenId);

		return $output;
	}
	

}
?>
