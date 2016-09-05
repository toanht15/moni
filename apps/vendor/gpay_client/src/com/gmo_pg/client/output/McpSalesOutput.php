<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>多通貨クレジットカード実売上　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class McpSalesOutput extends BaseOutput {

	/**
	 * @var string 取引ID
	 */
	var $accessID;
	/**
	 * @var string 取引パスワード
	 */
	var $accessPass;
	/**
	 * @var string 仕向先コード
	 */
	var $forward;
	/**
	 * @var string 承認番号
	 */
	var $approve;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function McpSalesOutput($params = null) {
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
		$this->setAccessID($params->get('AccessID'));
		$this->setAccessPass($params->get('AccessPass'));
		$this->setForward($params->get('Forward'));
		$this->setApprove($params->get('Approve'));

	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessID() {
		return $this->accessID;
	}
	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->accessPass;
	}
	/**
	 * 仕向先コード取得
	 * @return string 仕向先コード
	 */
	function getForward() {
		return $this->forward;
	}
	/**
	 * 承認番号取得
	 * @return string 承認番号
	 */
	function getApprove() {
		return $this->approve;
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->accessID = $accessID;
	}
	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->accessPass = $accessPass;
	}
	/**
	 * 仕向先コード設定
	 *
	 * @param string $forward
	 */
	function setForward($forward) {
		$this->forward = $forward;
	}
	/**
	 * 承認番号設定
	 *
	 * @param string $approve
	 */
	function setApprove($approve) {
		$this->approve = $approve;
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
		$str .= 'AccessID=' . $this->encodeStr($this->getAccessID());
		$str .='&';
		$str .= 'AccessPass=' . $this->encodeStr($this->getAccessPass());
		$str .='&';
		$str .= 'Forward=' . $this->encodeStr($this->getForward());
		$str .='&';
		$str .= 'Approve=' . $this->encodeStr($this->getApprove());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
