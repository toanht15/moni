<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>クレジットカード決済決済変更　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class AlterTranMagstripeOutput extends BaseOutput {

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
	 * @var string トランザクションID
	 */
	var $tranID;
	/**
	 * @var string 決済日付
	 */
	var $tranDate;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function AlterTranMagstripeOutput($params = null) {
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
		$this->setTranID($params->get('TranID'));
		$this->setTranDate($params->get('TranDate'));

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
	 * トランザクションID取得
	 * @return string トランザクションID
	 */
	function getTranID() {
		return $this->tranID;
	}
	/**
	 * 決済日付取得
	 * @return string 決済日付
	 */
	function getTranDate() {
		return $this->tranDate;
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
	 * トランザクションID設定
	 *
	 * @param string $tranID
	 */
	function setTranID($tranID) {
		$this->tranID = $tranID;
	}
	/**
	 * 決済日付設定
	 *
	 * @param string $tranDate
	 */
	function setTranDate($tranDate) {
		$this->tranDate = $tranDate;
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
		$str .='&';
		$str .= 'TranID=' . $this->encodeStr($this->getTranID());
		$str .='&';
		$str .= 'TranDate=' . $this->encodeStr($this->getTranDate());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
