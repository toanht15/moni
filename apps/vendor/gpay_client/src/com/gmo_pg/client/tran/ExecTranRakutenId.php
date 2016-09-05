<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/ExecTranRakutenIdOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>楽天ID決済実行　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecTranRakutenId extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function ExecTranRakutenId() {
	    parent::__construct();
	}
	
	/**
	 * 決済実行を実行する
	 *
	 * @param  ExecTranRakutenIdInput $input  入力パラメータ
	 * @return ExecTranRakutenIdOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // ExecTranRakutenIdOutput作成し、戻す
	    return new ExecTranRakutenIdOutput($resultMap);
	}
}
?>
