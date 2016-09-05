<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/McpCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>多通貨クレジットカードキャンセル　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class McpCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function McpCancel() {
	    parent::__construct();
	}
	
	/**
	 * キャンセルを実行する
	 *
	 * @param  McpCancelInput $input  入力パラメータ
	 * @return McpCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // McpCancelOutput作成し、戻す
	    return new McpCancelOutput($resultMap);
	}
}
?>
