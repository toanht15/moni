<?php
require_once 'com/gmo_pg/client/output/EntryExecTranMcpOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranMcp.php';
require_once 'com/gmo_pg/client/tran/ExecTranMcp.php';
/**
 * <b>多通貨クレジットカード登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranMcp {
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
	function EntryExecTranMcp() {
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
	 * 多通貨クレジットカード登録・決済を実行する
	 *
	 * @param EntryExecTranMcpInput $input    多通貨クレジットカード登録・決済入力パラメータ
	 * @return  EntryExecTranMcpOutput 多通貨クレジットカード登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// 多通貨クレジットカード取引登録入力パラメータを取得
		$entryTranMcpInput =& $input->getEntryTranMcpInput();
		// 多通貨クレジットカード決済実行入力パラメータを取得
		$execTranMcpInput =& $input->getExecTranMcpInput();

		// 多通貨クレジットカード登録・決済出力パラメータを生成
		$output = new EntryExecTranMcpOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranMcpInput->getAccessId();
		$accessPass = $execTranMcpInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// 多通貨クレジットカード取引登録を実行
			$this->log->debug("多通貨クレジットカード取引登録実行");
			$entryTranMcp = new EntryTranMcp();
			$entryTranMcpOutput = $entryTranMcp->exec($entryTranMcpInput);

			if ($this->errorTrap($entryTranMcp)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranMcpOutput->getAccessId();
			$accessPass = $entryTranMcpOutput->getAccessPass();
			$execTranMcpInput->setAccessId($accessId);
			$execTranMcpInput->setAccessPass($accessPass);

			$output->setEntryTranMcpOutput($entryTranMcpOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranMcp = new ExecTranMcp();
		$execTranMcpOutput = $execTranMcp->exec($execTranMcpInput);

		$output->setExecTranMcpOutput($execTranMcpOutput);

		$this->errorTrap($execTranMcp);

		return $output;
	}
	

}
?>
