<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>リクルートかんたん支払いオーソリ期限延長　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class RecruitUpdateAuthOutput extends BaseOutput {

	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var string 現状態
	 */
	var $status;
	/**
	 * @var string オーソリ期限延長実施日
	 */
	var $rcUpdateAuthDay;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function RecruitUpdateAuthOutput($params = null) {
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
		$this->setRcUpdateAuthDay($params->get('RcUpdateAuthDay'));

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
	 * オーソリ期限延長実施日取得
	 * @return string オーソリ期限延長実施日
	 */
	function getRcUpdateAuthDay() {
		return $this->rcUpdateAuthDay;
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
	 * オーソリ期限延長実施日設定
	 *
	 * @param string $rcUpdateAuthDay
	 */
	function setRcUpdateAuthDay($rcUpdateAuthDay) {
		$this->rcUpdateAuthDay = $rcUpdateAuthDay;
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
		$str .='&';
		$str .= 'RcUpdateAuthDay=' . $this->encodeStr($this->getRcUpdateAuthDay());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
