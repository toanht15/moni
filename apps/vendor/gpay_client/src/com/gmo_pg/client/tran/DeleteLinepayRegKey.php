<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/DeleteLinepayRegKeyOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>LINE PayRegKey満了　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class DeleteLinepayRegKey extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function DeleteLinepayRegKey() {
	    parent::__construct();
	}
	
	/**
	 * RegKey満了を実行する
	 *
	 * @param  DeleteLinepayRegKeyInput $input  入力パラメータ
	 * @return DeleteLinepayRegKeyOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // DeleteLinepayRegKeyOutput作成し、戻す
	    return new DeleteLinepayRegKeyOutput($resultMap);
	}
}
?>
