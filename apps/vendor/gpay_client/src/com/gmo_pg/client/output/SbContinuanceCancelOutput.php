<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>ソフトバンク継続解約　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class SbContinuanceCancelOutput extends BaseOutput {

	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var string 現状態
	 */
	var $status;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function SbContinuanceCancelOutput($params = null) {
		$this->__construct($params);
	}

	
	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function __construct($params = null) {
		parent::__construct($params);
		
		// 引数が無い場合は戻る
		if (is_null($params)) {
            return;
        }
		
        // マップの展開
		$this->setOrderID($params->get('OrderID'));
		$this->setStatus($params->get('Status'));

	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->orderID;
	}
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->status;
	}

	/**
	 * オーダーID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->orderID = $orderID;
	}
	/**
	 * 現状態設定
	 *
	 * @param string $status
	 */
	function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * 文字列表現
	 * <p>
	 *  現在の各パラメータを、パラメータ名=値&パラメータ名=値の形式で取得します。
	 * </p>
	 * @return string 出力パラメータの文字列表現
	 */
	function toString() {
		$str ='';
		$str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .='&';
		$str .= 'Status=' . $this->encodeStr($this->getStatus());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
