<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/RecruitCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>リクルートかんたん支払い決済キャンセル　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class RecruitCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function RecruitCancel() {
	    parent::__construct();
	}
	
	/**
	 * 決済キャンセルを実行する
	 *
	 * @param  RecruitCancelInput $input  入力パラメータ
	 * @return RecruitCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // RecruitCancelOutput作成し、戻す
	    return new RecruitCancelOutput($resultMap);
	}
}
?>
