<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/RakutenIdCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>楽天IDキャンセル　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class RakutenIdCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function RakutenIdCancel() {
	    parent::__construct();
	}
	
	/**
	 * キャンセルを実行する
	 *
	 * @param  RakutenIdCancelInput $input  入力パラメータ
	 * @return RakutenIdCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // RakutenIdCancelOutput作成し、戻す
	    return new RakutenIdCancelOutput($resultMap);
	}
}
?>
