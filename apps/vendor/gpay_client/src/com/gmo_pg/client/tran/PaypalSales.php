<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/PaypalSalesOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>Paypal決済実売上　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2014/01/21
 */
class PaypalSales extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function PaypalSales() {
	    parent::__construct();
	}
	
	/**
	 * 売上確定を実行する
	 *
	 * @param  PaypalSalesInput $input  入力パラメータ
	 * @return PaypalSalesOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // PaypalSalesOutput作成し、戻す
	    return new PaypalSalesOutput($resultMap);
	}
}
?>