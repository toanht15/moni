<?php
require_once 'com/gmo_pg/client/output/EntryExecTranRecruitContinuanceOutput.php';
require_once 'com/gmo_pg/client/tran/EntryTranRecruitContinuance.php';
require_once 'com/gmo_pg/client/tran/ExecTranRecruitContinuance.php';
/**
 * <b>リクルートかんたん支払い継続課金登録・決済一括実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryExecTranRecruitContinuance {
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
	function EntryExecTranRecruitContinuance() {
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
	 * リクルートかんたん支払い継続課金登録・決済を実行する
	 *
	 * @param EntryExecTranRecruitContinuanceInput $input    リクルートかんたん支払い継続課金登録・決済入力パラメータ
	 * @return  EntryExecTranRecruitContinuanceOutput リクルートかんたん支払い継続課金登録・決済出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
		// リクルートかんたん支払い継続課金取引登録入力パラメータを取得
		$entryTranRecruitContinuanceInput =& $input->getEntryTranRecruitContinuanceInput();
		// リクルートかんたん支払い継続課金決済実行入力パラメータを取得
		$execTranRecruitContinuanceInput =& $input->getExecTranRecruitContinuanceInput();

		// リクルートかんたん支払い継続課金登録・決済出力パラメータを生成
		$output = new EntryExecTranRecruitContinuanceOutput();

		// 取引ID、取引パスワードを取得
		$accessId = $execTranRecruitContinuanceInput->getAccessId();
		$accessPass = $execTranRecruitContinuanceInput->getAccessPass();

		// 取引ID、取引パスワードが設定されていないとき
		if (is_null($accessId) || 0 == strlen($accessId) || is_null($accessPass)) {
			// リクルートかんたん支払い継続課金取引登録を実行
			$this->log->debug("リクルートかんたん支払い継続課金取引登録実行");
			$entryTranRecruitContinuance = new EntryTranRecruitContinuance();
			$entryTranRecruitContinuanceOutput = $entryTranRecruitContinuance->exec($entryTranRecruitContinuanceInput);

			if ($this->errorTrap($entryTranRecruitContinuance)) {
				return $output;
			}

			// 取引ID、取引パスワードを決済実行用のパラメータに設定
			$accessId = $entryTranRecruitContinuanceOutput->getAccessId();
			$accessPass = $entryTranRecruitContinuanceOutput->getAccessPass();
			$execTranRecruitContinuanceInput->setAccessId($accessId);
			$execTranRecruitContinuanceInput->setAccessPass($accessPass);

			$output->setEntryTranRecruitContinuanceOutput($entryTranRecruitContinuanceOutput);
		}

		$this->log->debug("取引ID : [$accessId]  取引パスワード : [$accessPass]");

		// 取引登録でエラーが起きたとき決済を実行せずに戻る
		if ($output->isEntryErrorOccurred()) {
			$this->log->debug("<<<取引登録失敗>>>");
			return $output;
		}

		// 決済実行
		$this->log->debug("決済実行");
		$execTranRecruitContinuance = new ExecTranRecruitContinuance();
		$execTranRecruitContinuanceOutput = $execTranRecruitContinuance->exec($execTranRecruitContinuanceInput);

		$output->setExecTranRecruitContinuanceOutput($execTranRecruitContinuanceOutput);

		$this->errorTrap($execTranRecruitContinuance);

		return $output;
	}
	

}
?>
