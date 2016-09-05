<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/EntryTranRecruitContinuanceOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>リクルートかんたん支払い継続課金取引登録　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class EntryTranRecruitContinuance extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function EntryTranRecruitContinuance() {
	    parent::__construct();
	}
	
	/**
	 * 取引登録を実行する
	 *
	 * @param  EntryTranRecruitContinuanceInput $input  入力パラメータ
	 * @return EntryTranRecruitContinuanceOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // EntryTranRecruitContinuanceOutput作成し、戻す
	    return new EntryTranRecruitContinuanceOutput($resultMap);
	}
}
?>
