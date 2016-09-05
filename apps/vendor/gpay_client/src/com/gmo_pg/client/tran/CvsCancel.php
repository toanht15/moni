<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/CvsCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>コンビニキャンセル　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 3.110.122
 * @created 2015/3/23
 */
class CvsCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function CvsCancel() {
	    parent::__construct();
	}
	
	/**
	 * キャンセルを実行する
	 *
	 * @param  CvsCancelInput $input  入力パラメータ
	 * @return CvsCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // CvsCancelOutput作成し、戻す
	    return new CvsCancelOutput($resultMap);
	}
}
?>
