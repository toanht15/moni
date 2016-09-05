<?php
require_once ('com/gmo_pg/client/output/CancelAuthPaypalOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');

/**
 * <b>Paypal仮売上取消　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2014/01/21
 */
class CancelAuthPaypal extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function CancelAuthPaypal() {
		parent::__construct();
	}

	/**
	 * 払い戻しを実行する
	 *
	 * @param CancelAuthPaypalInput $input    入力パラメータ
	 * @return CancelAuthPaypalOutput 出力パラメータ
	 */
	function exec(&$input) {
		// プロトコル呼び出し・結果取得
		$resultMap = $this->callProtocol($input->toString());

		// 戻り値がnullの場合、nullを戻す
		if (is_null($resultMap)) {
			return null;
		}

		// CancelAuthPaypalOutput作成
		return new CancelAuthPaypalOutput($resultMap);
	}
}