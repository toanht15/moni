<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/SbCancelOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>ソフトバンクケータイ支払い決済取消　実行クラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2012/10/18
 */
class SbCancel extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function SbCancel() {
	    parent::__construct();
	}

	/**
	 * 決済取消を実行する
	 *
	 * @param  SbCancelInput $input  入力パラメータ
	 * @return SbCancelOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {

        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }

        // SbCancelOutput作成し、戻す
	    return new SbCancelOutput($resultMap);
	}
}
?>
