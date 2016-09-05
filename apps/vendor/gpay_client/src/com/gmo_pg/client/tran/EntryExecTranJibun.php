<?php
require_once 'com/gmo_pg/client/output/EntryExecTranJibunOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranJibun.php';
require_once 'com/gmo_pg/client/tran/ExecTranJibun.php';
/**
 * <b>じぶん銀行決済登録・決済一括実行　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/31
 */
class EntryExecTranJibun {
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
	function EntryExecTranJibun() {
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
	 * じぶん銀行決済登録・決済を実行する
	 *
	 * @param EntryExecTranJibunInput $input    じぶん銀行決済登録・決済入力パラメータ
	 * @return  EntryExecTranJibunOutput じぶん銀行決済登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// じぶん銀行決済取引登録入力パラメータを取得
		$entryTranJibunInput =& $input->getEntryTranJibunInput();
		// じぶん銀行決済決済実行入力パラメータを取得
		$execTranJibunInput =& $input->getExecTranJibunInput();

		// じぶん銀行決済登録・決済出力パラメータを生成
		$output = new EntryExecTranJibunOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranJibunInput->getAccessId();
		$accessPass = $execTranJibunInput->getAccessPass();
		//取引登録実行フラグ
		$entryFlg = false;
		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// じぶん銀行決済取引登録を実行
			$this->log->debug("じぶん銀行決済取引登録実行");
			$entryTranJibun = new EntryTranJibun();
			$entryTranJibunOutput = $entryTranJibun->exec($entryTranJibunInput);

			if ($this->errorTrap($entryTranJibun)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranJibunOutput->getAccessId();
			$accessPass = $entryTranJibunOutput->getAccessPass();
			$execTranJibunInput->setAccessId($accessId);
			$execTranJibunInput->setAccessPass($accessPass);

			$output->setEntryTranJibunOutput($entryTranJibunOutput);
			//実行ON
			$entryFlg = true;
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranJibun = new ExecTranJibun();
		$execTranJibunOutput = $execTranJibun->exec($execTranJibunInput);

		//処理が正常終了した場合
		//entryの出力モデルにアクセスIDとアクセスパスワードを格納
		//※決済実行のみの場合、アクセスIDとパスが格納されない為
		if(!$entryFlg){
			// じぶん銀行決済登録出力パラメータを生成
			$entryTranJibunOutput = new EntryTranJibunOutput();

			$entryTranJibunOutput->setAccessId($accessId);
			$entryTranJibunOutput->setAccessPass($accessPass);

			$output->setEntryTranJibunOutput($entryTranJibunOutput);
		}

		$output->setExecTranJibunOutput($execTranJibunOutput);

		$this->errorTrap($execTranJibun);

		return $output;
	}


}
?>
