<?php
require_once 'com/gmo_pg/client/output/EntryExecTranMagstripeOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTran.php';
require_once 'com/gmo_pg/client/tran/ExecTranMagstripe.php';
/**
 * <b>クレジットカード決済登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranMagstripe {
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
	function EntryExecTranMagstripe() {
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
	 * クレジットカード決済登録・決済を実行する
	 *
	 * @param EntryExecTranMagstripeInput $input    クレジットカード決済登録・決済入力パラメータ
	 * @return  EntryExecTranMagstripeOutput クレジットカード決済登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// クレジットカード決済取引登録入力パラメータを取得
		$entryTranInput =& $input->getEntryTranInput();
		// クレジットカード決済決済実行入力パラメータを取得
		$execTranMagstripeInput =& $input->getExecTranMagstripeInput();

		// クレジットカード決済登録・決済出力パラメータを生成
		$output = new EntryExecTranMagstripeOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranMagstripeInput->getAccessId();
		$accessPass = $execTranMagstripeInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// クレジットカード決済取引登録を実行
			$this->log->debug("クレジットカード決済取引登録実行");
			$entryTran = new EntryTran();
			$entryTranOutput = $entryTran->exec($entryTranInput);

			if ($this->errorTrap($entryTran)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranOutput->getAccessId();
			$accessPass = $entryTranOutput->getAccessPass();
			$execTranMagstripeInput->setAccessId($accessId);
			$execTranMagstripeInput->setAccessPass($accessPass);

			$output->setEntryTranOutput($entryTranOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranMagstripe = new ExecTranMagstripe();
		$execTranMagstripeOutput = $execTranMagstripe->exec($execTranMagstripeInput);

		$output->setExecTranMagstripeOutput($execTranMagstripeOutput);

		$this->errorTrap($execTranMagstripe);

		return $output;
	}
	

}
?>
