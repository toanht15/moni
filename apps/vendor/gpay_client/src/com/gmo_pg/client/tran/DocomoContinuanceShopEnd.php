<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/DocomoContinuanceShopEndOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>ドコモ継続決済　継続課金終了(加盟店)　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/08/21
 */
class DocomoContinuanceShopEnd extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function DocomoContinuanceShopEnd() {
	    parent::__construct();
	}

	/**
	 * 決済実行を実行する
	 *
	 * @param  DocomoContinuanceShopEndInput $input  入力パラメータ
	 * @return DocomoContinuanceShopEndOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // DocomoContinuanceShopEndOutput作成し、戻す
	    return new DocomoContinuanceShopEndOutput($resultMap);
	}
}
?>
