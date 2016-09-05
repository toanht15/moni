<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>自動売上自動売上結果照会　出力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class SearchRecurringResultOutput extends BaseOutput {

	/**
	 * @var string 課金手段
	 */
	var $method;
	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string 自動売上ID
	 */
	var $recurringID;
	/**
	 * @var string オーダーID
	 */
	var $orderID;
	/**
	 * @var string 課金日
	 */
	var $chargeDate;
	/**
	 * @var string 取引状態 または 振替依頼レコード状態
	 */
	var $status;
	/**
	 * @var integer 利用金額
	 */
	var $amount;
	/**
	 * @var integer 税送料
	 */
	var $tax;
	/**
	 * @var string 次回課金日
	 */
	var $nextChargeDate;
	/**
	 * @var string アクセスID
	 */
	var $accessID;
	/**
	 * @var string アクセスパスワード
	 */
	var $accessPass;
	/**
	 * @var string 仕向先
	 */
	var $forward;
	/**
	 * @var string 承認番号
	 */
	var $approvalNo;
	/**
	 * @var string サイトID
	 */
	var $siteID;
	/**
	 * @var string メンバーID
	 */
	var $memberID;
	/**
	 * @var string 通帳記載内容
	 */
	var $printStr;
	/**
	 * @var string 振替結果詳細コード
	 */
	var $result;
	/**
	 * @var string 自動売上エラーコード
	 */
	var $chargeErrCode;
	/**
	 * @var string 自動売上エラー詳細コード
	 */
	var $chargeErrInfo;
	/**
	 * @var string 処理日時
	 */
	var $processDate;

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function SearchRecurringResultOutput($params = null) {
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
		$this->setMethod($params->get('Method'));
		$this->setShopID($params->get('ShopID'));
		$this->setRecurringID($params->get('RecurringID'));
		$this->setOrderID($params->get('OrderID'));
		$this->setChargeDate($params->get('ChargeDate'));
		$this->setStatus($params->get('Status'));
		$this->setAmount($params->get('Amount'));
		$this->setTax($params->get('Tax'));
		$this->setNextChargeDate($params->get('NextChargeDate'));
		$this->setAccessID($params->get('AccessID'));
		$this->setAccessPass($params->get('AccessPass'));
		$this->setForward($params->get('Forward'));
		$this->setApprovalNo($params->get('ApprovalNo'));
		$this->setSiteID($params->get('SiteID'));
		$this->setMemberID($params->get('MemberID'));
		$this->setPrintStr($params->get('PrintStr'));
		$this->setResult($params->get('Result'));
		$this->setChargeErrCode($params->get('ChargeErrCode'));
		$this->setChargeErrInfo($params->get('ChargeErrInfo'));
		$this->setProcessDate($params->get('ProcessDate'));

	}

	/**
	 * 課金手段取得
	 * @return string 課金手段
	 */
	function getMethod() {
		return $this->method;
	}
	/**
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopID() {
		return $this->shopID;
	}
	/**
	 * 自動売上ID取得
	 * @return string 自動売上ID
	 */
	function getRecurringID() {
		return $this->recurringID;
	}
	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderID() {
		return $this->orderID;
	}
	/**
	 * 課金日取得
	 * @return string 課金日
	 */
	function getChargeDate() {
		return $this->chargeDate;
	}
	/**
	 * 取引状態 または 振替依頼レコード状態取得
	 * @return string 取引状態 または 振替依頼レコード状態
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
	 * 次回課金日取得
	 * @return string 次回課金日
	 */
	function getNextChargeDate() {
		return $this->nextChargeDate;
	}
	/**
	 * アクセスID取得
	 * @return string アクセスID
	 */
	function getAccessID() {
		return $this->accessID;
	}
	/**
	 * アクセスパスワード取得
	 * @return string アクセスパスワード
	 */
	function getAccessPass() {
		return $this->accessPass;
	}
	/**
	 * 仕向先取得
	 * @return string 仕向先
	 */
	function getForward() {
		return $this->forward;
	}
	/**
	 * 承認番号取得
	 * @return string 承認番号
	 */
	function getApprovalNo() {
		return $this->approvalNo;
	}
	/**
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteID() {
		return $this->siteID;
	}
	/**
	 * メンバーID取得
	 * @return string メンバーID
	 */
	function getMemberID() {
		return $this->memberID;
	}
	/**
	 * 通帳記載内容取得
	 * @return string 通帳記載内容
	 */
	function getPrintStr() {
		return $this->printStr;
	}
	/**
	 * 振替結果詳細コード取得
	 * @return string 振替結果詳細コード
	 */
	function getResult() {
		return $this->result;
	}
	/**
	 * 自動売上エラーコード取得
	 * @return string 自動売上エラーコード
	 */
	function getChargeErrCode() {
		return $this->chargeErrCode;
	}
	/**
	 * 自動売上エラー詳細コード取得
	 * @return string 自動売上エラー詳細コード
	 */
	function getChargeErrInfo() {
		return $this->chargeErrInfo;
	}
	/**
	 * 処理日時取得
	 * @return string 処理日時
	 */
	function getProcessDate() {
		return $this->processDate;
	}

	/**
	 * 課金手段設定
	 *
	 * @param string $method
	 */
	function setMethod($method) {
		$this->method = $method;
	}
	/**
	 * ショップID設定
	 *
	 * @param string $shopID
	 */
	function setShopID($shopID) {
		$this->shopID = $shopID;
	}
	/**
	 * 自動売上ID設定
	 *
	 * @param string $recurringID
	 */
	function setRecurringID($recurringID) {
		$this->recurringID = $recurringID;
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
	 * 課金日設定
	 *
	 * @param string $chargeDate
	 */
	function setChargeDate($chargeDate) {
		$this->chargeDate = $chargeDate;
	}
	/**
	 * 取引状態 または 振替依頼レコード状態設定
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
	 * 次回課金日設定
	 *
	 * @param string $nextChargeDate
	 */
	function setNextChargeDate($nextChargeDate) {
		$this->nextChargeDate = $nextChargeDate;
	}
	/**
	 * アクセスID設定
	 *
	 * @param string $accessID
	 */
	function setAccessID($accessID) {
		$this->accessID = $accessID;
	}
	/**
	 * アクセスパスワード設定
	 *
	 * @param string $accessPass
	 */
	function setAccessPass($accessPass) {
		$this->accessPass = $accessPass;
	}
	/**
	 * 仕向先設定
	 *
	 * @param string $forward
	 */
	function setForward($forward) {
		$this->forward = $forward;
	}
	/**
	 * 承認番号設定
	 *
	 * @param string $approvalNo
	 */
	function setApprovalNo($approvalNo) {
		$this->approvalNo = $approvalNo;
	}
	/**
	 * サイトID設定
	 *
	 * @param string $siteID
	 */
	function setSiteID($siteID) {
		$this->siteID = $siteID;
	}
	/**
	 * メンバーID設定
	 *
	 * @param string $memberID
	 */
	function setMemberID($memberID) {
		$this->memberID = $memberID;
	}
	/**
	 * 通帳記載内容設定
	 *
	 * @param string $printStr
	 */
	function setPrintStr($printStr) {
		$this->printStr = $printStr;
	}
	/**
	 * 振替結果詳細コード設定
	 *
	 * @param string $result
	 */
	function setResult($result) {
		$this->result = $result;
	}
	/**
	 * 自動売上エラーコード設定
	 *
	 * @param string $chargeErrCode
	 */
	function setChargeErrCode($chargeErrCode) {
		$this->chargeErrCode = $chargeErrCode;
	}
	/**
	 * 自動売上エラー詳細コード設定
	 *
	 * @param string $chargeErrInfo
	 */
	function setChargeErrInfo($chargeErrInfo) {
		$this->chargeErrInfo = $chargeErrInfo;
	}
	/**
	 * 処理日時設定
	 *
	 * @param string $processDate
	 */
	function setProcessDate($processDate) {
		$this->processDate = $processDate;
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
		$str .= 'Method=' . $this->encodeStr($this->getMethod());
		$str .='&';
		$str .= 'ShopID=' . $this->encodeStr($this->getShopID());
		$str .='&';
		$str .= 'RecurringID=' . $this->encodeStr($this->getRecurringID());
		$str .='&';
		$str .= 'OrderID=' . $this->encodeStr($this->getOrderID());
		$str .='&';
		$str .= 'ChargeDate=' . $this->encodeStr($this->getChargeDate());
		$str .='&';
		$str .= 'Status=' . $this->encodeStr($this->getStatus());
		$str .='&';
		$str .= 'Amount=' . $this->encodeStr($this->getAmount());
		$str .='&';
		$str .= 'Tax=' . $this->encodeStr($this->getTax());
		$str .='&';
		$str .= 'NextChargeDate=' . $this->encodeStr($this->getNextChargeDate());
		$str .='&';
		$str .= 'AccessID=' . $this->encodeStr($this->getAccessID());
		$str .='&';
		$str .= 'AccessPass=' . $this->encodeStr($this->getAccessPass());
		$str .='&';
		$str .= 'Forward=' . $this->encodeStr($this->getForward());
		$str .='&';
		$str .= 'ApprovalNo=' . $this->encodeStr($this->getApprovalNo());
		$str .='&';
		$str .= 'SiteID=' . $this->encodeStr($this->getSiteID());
		$str .='&';
		$str .= 'MemberID=' . $this->encodeStr($this->getMemberID());
		$str .='&';
		$str .= 'PrintStr=' . $this->encodeStr($this->getPrintStr());
		$str .='&';
		$str .= 'Result=' . $this->encodeStr($this->getResult());
		$str .='&';
		$str .= 'ChargeErrCode=' . $this->encodeStr($this->getChargeErrCode());
		$str .='&';
		$str .= 'ChargeErrInfo=' . $this->encodeStr($this->getChargeErrInfo());
		$str .='&';
		$str .= 'ProcessDate=' . $this->encodeStr($this->getProcessDate());

	    
	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }	    
	    
        return $str;
	}

}
?>
