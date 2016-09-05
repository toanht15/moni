<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/EntryTranJcbPrecaOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>JCBプリカ取引登録　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryTranJcbPreca extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function EntryTranJcbPreca() {
	    parent::__construct();
	}
	
	/**
	 * 取引登録を実行する
	 *
	 * @param  EntryTranJcbPrecaInput $input  入力パラメータ
	 * @return EntryTranJcbPrecaOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // EntryTranJcbPrecaOutput作成し、戻す
	    return new EntryTranJcbPrecaOutput($resultMap);
	}
}
?>
