<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/RecruitContinuanceCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>リクルートかんたん支払い継続課金解約　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class RecruitContinuanceCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function RecruitContinuanceCancel() {
	    parent::__construct();
	}
	
	/**
	 * 解約を実行する
	 *
	 * @param  RecruitContinuanceCancelInput $input  入力パラメータ
	 * @return RecruitContinuanceCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // RecruitContinuanceCancelOutput作成し、戻す
	    return new RecruitContinuanceCancelOutput($resultMap);
	}
}
?>
