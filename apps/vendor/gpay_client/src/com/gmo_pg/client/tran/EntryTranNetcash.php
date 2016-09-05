<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/EntryTranNetcashOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>NET CASH取引登録　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryTranNetcash extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function EntryTranNetcash() {
	    parent::__construct();
	}
	
	/**
	 * 取引登録を実行する
	 *
	 * @param  EntryTranNetcashInput $input  入力パラメータ
	 * @return EntryTranNetcashOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // EntryTranNetcashOutput作成し、戻す
	    return new EntryTranNetcashOutput($resultMap);
	}
}
?>
