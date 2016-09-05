<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/ExecFraudScreeningOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>不正防止実行　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecFraudScreening extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function ExecFraudScreening() {
	    parent::__construct();
	}

	/**
	 * 実行を実行する
	 *
	 * @param  ExecFraudScreeningInput $input  入力パラメータ
	 * @return ExecFraudScreeningOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // ExecFraudScreeningOutput作成し、戻す
	    return new ExecFraudScreeningOutput($resultMap);
	}
}
?>
