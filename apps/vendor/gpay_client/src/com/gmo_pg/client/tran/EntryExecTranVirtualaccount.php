<?php
require_once 'com/gmo_pg/client/output/EntryExecTranVirtualaccountOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranVirtualaccount.php';
require_once 'com/gmo_pg/client/tran/ExecTranVirtualaccount.php';
/**
 * <b>銀行振込(バーチャル口座)登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranVirtualaccount {
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
	function EntryExecTranVirtualaccount() {
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
	 * 銀行振込(バーチャル口座)登録・決済を実行する
	 *
	 * @param EntryExecTranVirtualaccountInput $input    銀行振込(バーチャル口座)登録・決済入力パラメータ
	 * @return  EntryExecTranVirtualaccountOutput 銀行振込(バーチャル口座)登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// 銀行振込(バーチャル口座)取引登録入力パラメータを取得
		$entryTranVirtualaccountInput =& $input->getEntryTranVirtualaccountInput();
		// 銀行振込(バーチャル口座)決済実行入力パラメータを取得
		$execTranVirtualaccountInput =& $input->getExecTranVirtualaccountInput();

		// 銀行振込(バーチャル口座)登録・決済出力パラメータを生成
		$output = new EntryExecTranVirtualaccountOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranVirtualaccountInput->getAccessId();
		$accessPass = $execTranVirtualaccountInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// 銀行振込(バーチャル口座)取引登録を実行
			$this->log->debug("銀行振込(バーチャル口座)取引登録実行");
			$entryTranVirtualaccount = new EntryTranVirtualaccount();
			$entryTranVirtualaccountOutput = $entryTranVirtualaccount->exec($entryTranVirtualaccountInput);

			if ($this->errorTrap($entryTranVirtualaccount)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranVirtualaccountOutput->getAccessId();
			$accessPass = $entryTranVirtualaccountOutput->getAccessPass();
			$execTranVirtualaccountInput->setAccessId($accessId);
			$execTranVirtualaccountInput->setAccessPass($accessPass);

			$output->setEntryTranVirtualaccountOutput($entryTranVirtualaccountOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranVirtualaccount = new ExecTranVirtualaccount();
		$execTranVirtualaccountOutput = $execTranVirtualaccount->exec($execTranVirtualaccountInput);

		$output->setExecTranVirtualaccountOutput($execTranVirtualaccountOutput);

		$this->errorTrap($execTranVirtualaccount);

		return $output;
	}
	

}
?>
