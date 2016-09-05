<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/PayEasyCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>PayEasyキャンセル　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class PayEasyCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function PayEasyCancel() {
	    parent::__construct();
	}

	/**
	 * キャンセルを実行する
	 *
	 * @param  PayEasyCancelInput $input  入力パラメータ
	 * @return PayEasyCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // PayEasyCancelOutput作成し、戻す
	    return new PayEasyCancelOutput($resultMap);
	}
}
?>
