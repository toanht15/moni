<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/AlterTranMagstripeOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>クレジットカード決済決済変更　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class AlterTranMagstripe extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function AlterTranMagstripe() {
	    parent::__construct();
	}
	
	/**
	 * 決済変更を実行する
	 *
	 * @param  AlterTranMagstripeInput $input  入力パラメータ
	 * @return AlterTranMagstripeOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // AlterTranMagstripeOutput作成し、戻す
	    return new AlterTranMagstripeOutput($resultMap);
	}
}
?>
