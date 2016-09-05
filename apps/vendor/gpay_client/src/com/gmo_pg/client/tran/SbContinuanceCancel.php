<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/SbContinuanceCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>ソフトバンク継続解約　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class SbContinuanceCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function SbContinuanceCancel() {
	    parent::__construct();
	}
	
	/**
	 * 解約を実行する
	 *
	 * @param  SbContinuanceCancelInput $input  入力パラメータ
	 * @return SbContinuanceCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // SbContinuanceCancelOutput作成し、戻す
	    return new SbContinuanceCancelOutput($resultMap);
	}
}
?>
