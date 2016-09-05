<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/ExecTranJcbPrecaOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>JCBプリカ決済実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranJcbPreca extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function ExecTranJcbPreca() {
	    parent::__construct();
	}
	
	/**
	 * 決済実行を実行する
	 *
	 * @param  ExecTranJcbPrecaInput $input  入力パラメータ
	 * @return ExecTranJcbPrecaOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // ExecTranJcbPrecaOutput作成し、戻す
	    return new ExecTranJcbPrecaOutput($resultMap);
	}
}
?>
