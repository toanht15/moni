<?php
require_once ('com/gmo_pg/client/output/SearchCardReverseOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>逆引き会員ID検索　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 15-12-2014 00:00:00
 */
class SearchCardReverse extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function SearchCard() {
	    parent::__construct();
	}

	/**
	 * カードを照会する
	 *
	 * @param  SearchCardReverseInput $input    入力パラメータ
	 * @return SearchCardReverseOutput 出力パラメータ
	 */
	function exec(&$input) {
        // プロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
        
        // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
				
		// SearchCardReverseOutputを作成し、戻す
		return new SearchCardReverseOutput($resultMap);
	}

}
?>