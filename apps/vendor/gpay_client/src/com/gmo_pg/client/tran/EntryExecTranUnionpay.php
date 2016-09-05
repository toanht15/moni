<?php
require_once 'com/gmo_pg/client/output/EntryExecTranUnionpayOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranUnionpay.php';
require_once 'com/gmo_pg/client/tran/ExecTranUnionpay.php';
/**
 * <b>ネット銀聯登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranUnionpay {
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
	function EntryExecTranUnionpay() {
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
	 * ネット銀聯登録・決済を実行する
	 *
	 * @param EntryExecTranUnionpayInput $input    ネット銀聯登録・決済入力パラメータ
	 * @return  EntryExecTranUnionpayOutput ネット銀聯登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// ネット銀聯取引登録入力パラメータを取得
		$entryTranUnionpayInput =& $input->getEntryTranUnionpayInput();
		// ネット銀聯決済実行入力パラメータを取得
		$execTranUnionpayInput =& $input->getExecTranUnionpayInput();

		// ネット銀聯登録・決済出力パラメータを生成
		$output = new EntryExecTranUnionpayOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranUnionpayInput->getAccessId();
		$accessPass = $execTranUnionpayInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// ネット銀聯取引登録を実行
			$this->log->debug("ネット銀聯取引登録実行");
			$entryTranUnionpay = new EntryTranUnionpay();
			$entryTranUnionpayOutput = $entryTranUnionpay->exec($entryTranUnionpayInput);

			if ($this->errorTrap($entryTranUnionpay)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranUnionpayOutput->getAccessId();
			$accessPass = $entryTranUnionpayOutput->getAccessPass();
			$execTranUnionpayInput->setAccessId($accessId);
			$execTranUnionpayInput->setAccessPass($accessPass);

			$output->setEntryTranUnionpayOutput($entryTranUnionpayOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranUnionpay = new ExecTranUnionpay();
		$execTranUnionpayOutput = $execTranUnionpay->exec($execTranUnionpayInput);

		$output->setExecTranUnionpayOutput($execTranUnionpayOutput);

		$this->errorTrap($execTranUnionpay);

		return $output;
	}
	

}
?>
