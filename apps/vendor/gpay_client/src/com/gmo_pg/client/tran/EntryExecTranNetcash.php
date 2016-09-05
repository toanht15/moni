<?php
require_once 'com/gmo_pg/client/output/EntryExecTranNetcashOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranNetcash.php';
require_once 'com/gmo_pg/client/tran/ExecTranNetcash.php';
/**
 * <b>NET CASH登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranNetcash {
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
	function EntryExecTranNetcash() {
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
	 * NET CASH登録・決済を実行する
	 *
	 * @param EntryExecTranNetcashInput $input    NET CASH登録・決済入力パラメータ
	 * @return  EntryExecTranNetcashOutput NET CASH登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// NET CASH取引登録入力パラメータを取得
		$entryTranNetcashInput =& $input->getEntryTranNetcashInput();
		// NET CASH決済実行入力パラメータを取得
		$execTranNetcashInput =& $input->getExecTranNetcashInput();

		// NET CASH登録・決済出力パラメータを生成
		$output = new EntryExecTranNetcashOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranNetcashInput->getAccessId();
		$accessPass = $execTranNetcashInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// NET CASH取引登録を実行
			$this->log->debug("NET CASH取引登録実行");
			$entryTranNetcash = new EntryTranNetcash();
			$entryTranNetcashOutput = $entryTranNetcash->exec($entryTranNetcashInput);

			if ($this->errorTrap($entryTranNetcash)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranNetcashOutput->getAccessId();
			$accessPass = $entryTranNetcashOutput->getAccessPass();
			$execTranNetcashInput->setAccessId($accessId);
			$execTranNetcashInput->setAccessPass($accessPass);

			$output->setEntryTranNetcashOutput($entryTranNetcashOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranNetcash = new ExecTranNetcash();
		$execTranNetcashOutput = $execTranNetcash->exec($execTranNetcashInput);

		$output->setExecTranNetcashOutput($execTranNetcashOutput);

		$this->errorTrap($execTranNetcash);

		return $output;
	}
	

}
?>
