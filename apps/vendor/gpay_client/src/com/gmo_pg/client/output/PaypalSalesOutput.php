<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>Paypal決済実売上　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2014/01/21
 */
class PaypalSalesOutput extends BaseOutput {

	/**
	 * @var string オーダID
	 */
	var $orderID;

	/**
	 * @var string トランザクションID
	 */
	var $tranId;

	/**
	 * @var string 決済日付
	 */
	var $tranDate;
	
	/**
	 * @var string 現状態
	 */
	var $status;

	/**
	 * @var string 利用金額
	 */
	var $amount;

	/**
	 * @var string 税送料
	 */
	var $tax;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function PaypalSalesOutput($params = null) {
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
		$this->setTranId($params->get('TranID'));
		$this->setTranDate($params->get('TranDate'));
        $this->setStatus($params->get('Status'));
        $this->setAmount($params->get('Amount'));
        $this->setTax($params->get('Tax'));
	}

	/**
	 * オーダID取得
	 * @return string オーダID
	 */
	function getOrderID() {
		return $this->orderID;
	}

	/**
	 * トランザクションIDを取得する
	 * @return string トランザクションID
	 */
	function getTranId(){
		return $this->tranId;
	}

	/**
	 * 決済日付を取得する
	 * @return string 決済日付
	 */
	function getTranDate(){
		return $this->tranDate;
	}
	
	/**
	 * 現状態取得
	 * @return string 現状態
	 */
	function getStatus() {
		return $this->status;
	}

	/**
	 * 利用金額取得
	 * @return integer 利用金額
	 */
	function getAmount() {
		return $this->amount;
	}

	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax() {
		return $this->tax;
	}

	/**
	 * オーダID設定
	 *
	 * @param string $orderID
	 */
	function setOrderID($orderID) {
		$this->orderID = $orderID;
	}
	
	/**
	 * トランザクションIDを設定する
	 * @param string $tranId トランザクションID
	 */
	function setTranId( $tranId ){
		$this->tranId = $tranId;
	}

	/**
	 * 決済日付を設定する
	 * @param string $tranDate 決済日付
	 */
	function setTranDate( $tranDate ){
		$this->tranDate = $tranDate;
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
	 * 利用金額設定
	 *
	 * @param integer $amount
	 */
	function setAmount($amount) {
		$this->amount = $amount;
	}

	/**
	 * 税送料設定
	 *
	 * @param integer $tax
	 */
	function setTax($tax) {
		$this->tax = $tax;
	}

	/**
	 * 文字列表現
	 * <p>
	 *  現在の各パラメータを、パラメータ名=値&パラメータ名=値の形式で取得します。
	 * </p>
	 * @return string 出力パラメータの文字列表現
	 */
	function toString() {
	    $str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .= '&';
		$str .= 'TranID=' . $this->getTranId();
		$str .= '&';
		$str .= 'TranDate=' . $this->getTranDate();
	    $str .= '&';
	    $str .= 'Status=' . $this->encodeStr($this->getStatus());
	    $str .= '&';
	    $str .= 'Amount=' . $this->encodeStr($this->getAmount());
	    $str .= '&';
	    $str .= 'Tax=' . $this->encodeStr($this->getTax());
	    
	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>