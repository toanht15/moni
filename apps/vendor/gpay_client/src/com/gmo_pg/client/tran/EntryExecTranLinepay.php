<?php
require_once 'com/gmo_pg/client/output/EntryExecTranLinepayOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranLinepay.php';
require_once 'com/gmo_pg/client/tran/ExecTranLinepay.php';
/**
 * <b>LINE Pay登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranLinepay {
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
	function EntryExecTranLinepay() {
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
	 * LINE Pay登録・決済を実行する
	 *
	 * @param EntryExecTranLinepayInput $input    LINE Pay登録・決済入力パラメータ
	 * @return  EntryExecTranLinepayOutput LINE Pay登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// LINE Pay取引登録入力パラメータを取得
		$entryTranLinepayInput =& $input->getEntryTranLinepayInput();
		// LINE Pay決済実行入力パラメータを取得
		$execTranLinepayInput =& $input->getExecTranLinepayInput();

		// LINE Pay登録・決済出力パラメータを生成
		$output = new EntryExecTranLinepayOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranLinepayInput->getAccessId();
		$accessPass = $execTranLinepayInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// LINE Pay取引登録を実行
			$this->log->debug("LINE Pay取引登録実行");
			$entryTranLinepay = new EntryTranLinepay();
			$entryTranLinepayOutput = $entryTranLinepay->exec($entryTranLinepayInput);

			if ($this->errorTrap($entryTranLinepay)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranLinepayOutput->getAccessId();
			$accessPass = $entryTranLinepayOutput->getAccessPass();
			$execTranLinepayInput->setAccessId($accessId);
			$execTranLinepayInput->setAccessPass($accessPass);

			$output->setEntryTranLinepayOutput($entryTranLinepayOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranLinepay = new ExecTranLinepay();
		$execTranLinepayOutput = $execTranLinepay->exec($execTranLinepayInput);

		$output->setExecTranLinepayOutput($execTranLinepayOutput);

		$this->errorTrap($execTranLinepay);

		return $output;
	}
	

}
?>
