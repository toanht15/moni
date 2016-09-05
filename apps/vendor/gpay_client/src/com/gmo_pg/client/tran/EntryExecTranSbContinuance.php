<?php
require_once 'com/gmo_pg/client/output/EntryExecTranSbContinuanceOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranSbContinuance.php';
require_once 'com/gmo_pg/client/tran/ExecTranSbContinuance.php';
/**
 * <b>ソフトバンク継続登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranSbContinuance {
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
	function EntryExecTranSbContinuance() {
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
	 * ソフトバンク継続登録・決済を実行する
	 *
	 * @param EntryExecTranSbContinuanceInput $input    ソフトバンク継続登録・決済入力パラメータ
	 * @return  EntryExecTranSbContinuanceOutput ソフトバンク継続登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// ソフトバンク継続取引登録入力パラメータを取得
		$entryTranSbContinuanceInput =& $input->getEntryTranSbContinuanceInput();
		// ソフトバンク継続決済実行入力パラメータを取得
		$execTranSbContinuanceInput =& $input->getExecTranSbContinuanceInput();

		// ソフトバンク継続登録・決済出力パラメータを生成
		$output = new EntryExecTranSbContinuanceOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranSbContinuanceInput->getAccessId();
		$accessPass = $execTranSbContinuanceInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// ソフトバンク継続取引登録を実行
			$this->log->debug("ソフトバンク継続取引登録実行");
			$entryTranSbContinuance = new EntryTranSbContinuance();
			$entryTranSbContinuanceOutput = $entryTranSbContinuance->exec($entryTranSbContinuanceInput);

			if ($this->errorTrap($entryTranSbContinuance)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranSbContinuanceOutput->getAccessId();
			$accessPass = $entryTranSbContinuanceOutput->getAccessPass();
			$execTranSbContinuanceInput->setAccessId($accessId);
			$execTranSbContinuanceInput->setAccessPass($accessPass);

			$output->setEntryTranSbContinuanceOutput($entryTranSbContinuanceOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranSbContinuance = new ExecTranSbContinuance();
		$execTranSbContinuanceOutput = $execTranSbContinuance->exec($execTranSbContinuanceInput);

		$output->setExecTranSbContinuanceOutput($execTranSbContinuanceOutput);

		$this->errorTrap($execTranSbContinuance);

		return $output;
	}
	

}
?>
