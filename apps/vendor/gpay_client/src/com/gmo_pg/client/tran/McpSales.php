<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/McpSalesOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>多通貨クレジットカード実売上　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class McpSales extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function McpSales() {
	    parent::__construct();
	}
	
	/**
	 * 実売上を実行する
	 *
	 * @param  McpSalesInput $input  入力パラメータ
	 * @return McpSalesOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // McpSalesOutput作成し、戻す
	    return new McpSalesOutput($resultMap);
	}
}
?>
