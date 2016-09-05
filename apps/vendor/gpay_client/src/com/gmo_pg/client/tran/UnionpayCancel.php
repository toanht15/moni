<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/UnionpayCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>ネット銀聯キャンセル　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class UnionpayCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function UnionpayCancel() {
	    parent::__construct();
	}
	
	/**
	 * キャンセルを実行する
	 *
	 * @param  UnionpayCancelInput $input  入力パラメータ
	 * @return UnionpayCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // UnionpayCancelOutput作成し、戻す
	    return new UnionpayCancelOutput($resultMap);
	}
}
?>
