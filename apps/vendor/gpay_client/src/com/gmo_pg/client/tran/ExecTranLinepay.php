<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/ExecTranLinepayOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>LINE Pay決済実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranLinepay extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function ExecTranLinepay() {
	    parent::__construct();
	}
	
	/**
	 * 決済実行を実行する
	 *
	 * @param  ExecTranLinepayInput $input  入力パラメータ
	 * @return ExecTranLinepayOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // ExecTranLinepayOutput作成し、戻す
	    return new ExecTranLinepayOutput($resultMap);
	}
}
?>
