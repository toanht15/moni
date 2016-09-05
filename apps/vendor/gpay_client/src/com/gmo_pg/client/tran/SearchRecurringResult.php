<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/SearchRecurringResultOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>自動売上自動売上結果照会　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class SearchRecurringResult extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function SearchRecurringResult() {
	    parent::__construct();
	}
	
	/**
	 * 自動売上結果照会を実行する
	 *
	 * @param  SearchRecurringResultInput $input  入力パラメータ
	 * @return SearchRecurringResultOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // SearchRecurringResultOutput作成し、戻す
	    return new SearchRecurringResultOutput($resultMap);
	}
}
?>
