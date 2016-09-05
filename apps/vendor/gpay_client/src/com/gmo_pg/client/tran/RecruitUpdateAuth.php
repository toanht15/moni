<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/RecruitUpdateAuthOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>リクルートかんたん支払いオーソリ期限延長　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class RecruitUpdateAuth extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function RecruitUpdateAuth() {
	    parent::__construct();
	}
	
	/**
	 * オーソリ期限延長を実行する
	 *
	 * @param  RecruitUpdateAuthInput $input  入力パラメータ
	 * @return RecruitUpdateAuthOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // RecruitUpdateAuthOutput作成し、戻す
	    return new RecruitUpdateAuthOutput($resultMap);
	}
}
?>
