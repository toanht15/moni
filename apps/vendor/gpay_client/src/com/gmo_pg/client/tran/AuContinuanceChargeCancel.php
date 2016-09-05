<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/AuContinuanceChargeCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>auかんたん決済継続課金決済取消　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2013/06/05
 */
class AuContinuanceChargeCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function AuContinuanceChargeCancel() {
	    parent::__construct();
	}

	/**
	 * 決済取消を実行する
	 *
	 * @param  AuContinuanceChargeCancelInput $input  入力パラメータ
	 * @return AuContinuanceChargeCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // AuContinuanceChargeCancelOutput作成し、戻す
	    return new AuContinuanceChargeCancelOutput($resultMap);
	}
}
?>