<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/UnionpayReturnOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>ネット銀聯返品　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class UnionpayReturn extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function UnionpayReturn() {
	    parent::__construct();
	}
	
	/**
	 * 返品を実行する
	 *
	 * @param  UnionpayReturnInput $input  入力パラメータ
	 * @return UnionpayReturnOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // UnionpayReturnOutput作成し、戻す
	    return new UnionpayReturnOutput($resultMap);
	}
}
?>
