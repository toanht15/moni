<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/MaillinkstartOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>メールリンク決済開始　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class Maillinkstart extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function Maillinkstart() {
	    parent::__construct();
	}
	
	/**
	 * 決済開始を実行する
	 *
	 * @param  MaillinkstartInput $input  入力パラメータ
	 * @return MaillinkstartOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // MaillinkstartOutput作成し、戻す
	    return new MaillinkstartOutput($resultMap);
	}
}
?>
