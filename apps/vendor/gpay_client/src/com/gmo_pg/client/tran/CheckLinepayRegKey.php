<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/CheckLinepayRegKeyOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>LINE PayRegKey利用可否チェック　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class CheckLinepayRegKey extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function CheckLinepayRegKey() {
	    parent::__construct();
	}
	
	/**
	 * RegKey利用可否チェックを実行する
	 *
	 * @param  CheckLinepayRegKeyInput $input  入力パラメータ
	 * @return CheckLinepayRegKeyOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // CheckLinepayRegKeyOutput作成し、戻す
	    return new CheckLinepayRegKeyOutput($resultMap);
	}
}
?>
