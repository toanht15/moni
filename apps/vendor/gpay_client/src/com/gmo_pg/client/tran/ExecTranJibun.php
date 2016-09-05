<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/ExecTranJibunOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>じぶん銀行決済決済実行　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/31
 */
class ExecTranJibun extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function ExecTranJibun() {
	    parent::__construct();
	}

	/**
	 * 決済実行を実行する
	 *
	 * @param  ExecTranJibunInput $input  入力パラメータ
	 * @return ExecTranJibunOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // ExecTranJibunOutput作成し、戻す
	    return new ExecTranJibunOutput($resultMap);
	}
}
?>
