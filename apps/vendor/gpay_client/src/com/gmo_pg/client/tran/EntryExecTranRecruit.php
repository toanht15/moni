<?php
require_once 'com/gmo_pg/client/output/EntryExecTranRecruitOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranRecruit.php';
require_once 'com/gmo_pg/client/tran/ExecTranRecruit.php';
/**
 * <b>リクルートかんたん支払い登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRecruit {
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
	function EntryExecTranRecruit() {
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
	 * リクルートかんたん支払い登録・決済を実行する
	 *
	 * @param EntryExecTranRecruitInput $input    リクルートかんたん支払い登録・決済入力パラメータ
	 * @return  EntryExecTranRecruitOutput リクルートかんたん支払い登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// リクルートかんたん支払い取引登録入力パラメータを取得
		$entryTranRecruitInput =& $input->getEntryTranRecruitInput();
		// リクルートかんたん支払い決済実行入力パラメータを取得
		$execTranRecruitInput =& $input->getExecTranRecruitInput();

		// リクルートかんたん支払い登録・決済出力パラメータを生成
		$output = new EntryExecTranRecruitOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranRecruitInput->getAccessId();
		$accessPass = $execTranRecruitInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// リクルートかんたん支払い取引登録を実行
			$this->log->debug("リクルートかんたん支払い取引登録実行");
			$entryTranRecruit = new EntryTranRecruit();
			$entryTranRecruitOutput = $entryTranRecruit->exec($entryTranRecruitInput);

			if ($this->errorTrap($entryTranRecruit)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranRecruitOutput->getAccessId();
			$accessPass = $entryTranRecruitOutput->getAccessPass();
			$execTranRecruitInput->setAccessId($accessId);
			$execTranRecruitInput->setAccessPass($accessPass);

			$output->setEntryTranRecruitOutput($entryTranRecruitOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranRecruit = new ExecTranRecruit();
		$execTranRecruitOutput = $execTranRecruit->exec($execTranRecruitInput);

		$output->setExecTranRecruitOutput($execTranRecruitOutput);

		$this->errorTrap($execTranRecruit);

		return $output;
	}
	

}
?>
