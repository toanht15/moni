<?php
require_once ('com/gmo_pg/client/common/Cryptgram.php');
require_once ('com/gmo_pg/client/common/GPayException.php');
require_once ('com/gmo_pg/client/output/InquiryVirtualaccountTransferOutput.php');
require_once ('com/gmo_pg/client/tran/BaseTran.php');
/**
 * <b>銀行振込(バーチャル口座)専有口座入金履歴取得　実行クラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage tran
 * @see tranPackageInfo.php
 * @author GMO PaymentGateway
 */
class InquiryVirtualaccountTransfer extends BaseTran {

	/**
	 * コンストラクタ
	 */
	function InquiryVirtualaccountTransfer() {
	    parent::__construct();
	}
	
	/**
	 * 専有口座入金履歴取得を実行する
	 *
	 * @param  InquiryVirtualaccountTransferInput $input  入力パラメータ
	 * @return InquiryVirtualaccountTransferOutput $output 出力パラメータ
	 * @exception GPayException
	 */
	function exec(&$input) {
	    
        // 接続しプロトコル呼び出し・結果取得
        $resultMap = $this->callProtocol($input->toString());
	    // 戻り値がnullの場合、nullを戻す
        if (is_null($resultMap)) {
		    return null;
        }
	    
        // InquiryVirtualaccountTransferOutput作成し、戻す
	    return new InquiryVirtualaccountTransferOutput($resultMap);
	}
}
?>
