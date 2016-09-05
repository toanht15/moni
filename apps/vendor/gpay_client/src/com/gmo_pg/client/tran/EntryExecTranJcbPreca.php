<?php
require_once 'com/gmo_pg/client/output/EntryExecTranJcbPrecaOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranJcbPreca.php';
require_once 'com/gmo_pg/client/tran/ExecTranJcbPreca.php';
/**
 * <b>JCBプリカ登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranJcbPreca {
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
	function EntryExecTranJcbPreca() {
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
	 * JCBプリカ登録・決済を実行する
	 *
	 * @param EntryExecTranJcbPrecaInput $input    JCBプリカ登録・決済入力パラメータ
	 * @return  EntryExecTranJcbPrecaOutput JCBプリカ登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// JCBプリカ取引登録入力パラメータを取得
		$entryTranJcbPrecaInput =& $input->getEntryTranJcbPrecaInput();
		// JCBプリカ決済実行入力パラメータを取得
		$execTranJcbPrecaInput =& $input->getExecTranJcbPrecaInput();

		// JCBプリカ登録・決済出力パラメータを生成
		$output = new EntryExecTranJcbPrecaOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranJcbPrecaInput->getAccessId();
		$accessPass = $execTranJcbPrecaInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// JCBプリカ取引登録を実行
			$this->log->debug("JCBプリカ取引登録実行");
			$entryTranJcbPreca = new EntryTranJcbPreca();
			$entryTranJcbPrecaOutput = $entryTranJcbPreca->exec($entryTranJcbPrecaInput);

			if ($this->errorTrap($entryTranJcbPreca)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranJcbPrecaOutput->getAccessId();
			$accessPass = $entryTranJcbPrecaOutput->getAccessPass();
			$execTranJcbPrecaInput->setAccessId($accessId);
			$execTranJcbPrecaInput->setAccessPass($accessPass);

			$output->setEntryTranJcbPrecaOutput($entryTranJcbPrecaOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranJcbPreca = new ExecTranJcbPreca();
		$execTranJcbPrecaOutput = $execTranJcbPreca->exec($execTranJcbPrecaInput);

		$output->setExecTranJcbPrecaOutput($execTranJcbPrecaOutput);

		$this->errorTrap($execTranJcbPreca);

		return $output;
	}
	

}
?>
