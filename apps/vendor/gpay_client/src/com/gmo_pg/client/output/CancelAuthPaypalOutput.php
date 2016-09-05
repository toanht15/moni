<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');

/**
 * <b>Paypal仮売上取消 出力パラメータクラス</b>
 *
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 2014/01/21
 */
class CancelAuthPaypalOutput extends BaseOutput {

	/**
	 * @var string オーダID
	 */
	var $orderId;

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
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function CancelAuthPaypalOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */

	function __construct($params = null) {
		parent::__construct($params);

		// 引数がない場合は戻る
		if(is_null($params)) {
			return;
		}

		// マップの展開
		$this->setOrderId($params->get('OrderID'));
		$this->setTranId($params->get('TranID'));
		$this->setTranDate($params->get('TranDate'));
        $this->setStatus($params->get('Status'));
	}


	/**
	 * オーダIDを取得する
	 * @return string オーダID
	 */
	function getOrderId(){
		return $this->orderId;
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
	 * オーダIDを設定する
	 * @param $orderId オーダID
	 */
	function setOrderId( $orderId ){
		$this->orderId = $orderId;
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
	 * 文字列表現
	 * <p>
	 *  現在の各パラメータを、パラメータ名=値&パラメータ名=値の形式で取得します。
	 * </p>
	 * @return string 出力パラメータの文字列表現
	 */
	function toString() {
		$str  = 'OrderID=' . $this->getOrderId();
		$str .= '&';
		$str .= 'TranID=' . $this->getTranIdId();
		$str .= '&';
		$str .= 'TranDate=' . $this->getTranDate();
	    $str .= '&';
	    $str .= 'Status=' . $this->encodeStr($this->getStatus());
			
		if ($this->isErrorOccurred()) {
			// エラー文字列を連結して返す
			$errString = parent::toString();
			$str .= '&' . $errString;
		}
		return $str;
	}
}