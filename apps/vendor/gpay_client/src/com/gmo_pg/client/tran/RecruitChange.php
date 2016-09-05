<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/RecruitChangeOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>リクルートかんたん支払い金額変更　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class RecruitChange extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function RecruitChange() {
	    parent::__construct();
	}
	
	/**
	 * 金額変更を実行する
	 *
	 * @param  RecruitChangeInput $input  入力パラメータ
	 * @return RecruitChangeOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // RecruitChangeOutput作成し、戻す
	    return new RecruitChangeOutput($resultMap);
	}
}
?>
