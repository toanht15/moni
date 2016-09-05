<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
/**
 * <b>実売予約照会　出力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 10-22-2012 00:00:00
 */
class SearchBookingInfoOutput extends BaseOutput {

	/**
	 * @var string 取引ID
	 */
	var $accessId;

	/**
	 * @var string 取引パスワード
	 */
	var $accessPass;

	/**
	 * @var string 予約情報バージョン
	 */
	var $bookingInfoVersion;

	/**
	 * @var string 予約ステータス
	 */
	var $bookingStatus;

	/**
	 * @var string 実売予約日
	 */
	var $bookingDate;

	/**
	 * @var string 処理結果仕向先コード
	 */
	var $resultForward;

	/**
	 * @var string 処理結果承認番号
	 */
	var $resultApprove;

	/**
	 * @var string 処理結果トランザクションID
	 */
	var $resultTranId;

	/**
	 * @var string 処理結果決済日付
	 */
	var $resultTranDate;

	/**
	 * @var string 処理結果エラーコード
	 */
	var $resultErrCode;

	/**
	 * @var string 処理結果エラー詳細コード
	 */
	var $resultErrInfo;


	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params  出力パラメータ
	 */
	function SearchBookingInfoOutput($params = null) {
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
        $this->setAccessId($params->get('AccessID'));
        $this->setAccessPass($params->get('AccessPass'));
        $this->setBookingInfoVersion($params->get('BookingInfoVersion'));
        $this->setBookingStatus($params->get('BookingStatus'));
        $this->setBookingDate($params->get('BookingDate'));
        $this->setResultForward($params->get('ResultForward'));
        $this->setResultApprove($params->get('ResultApprove'));
        $this->setResultTranId($params->get('ResultTranId'));
        $this->setResultTranDate($params->get('ResultTranDate'));
        $this->setResultErrCode($params->get('ResultErrCode'));
        $this->setResultErrInfo($params->get('ResultErrInfo'));
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessId() {
		return $this->accessId;
	}

	/**
	 * 取引パスワード取得
	 * @return string 取引パスワード
	 */
	function getAccessPass() {
		return $this->accessPass;
	}

	/**
	 * 予約情報バージョン取得
	 * @return string 予約情報バージョン
	 */
	function getBookingInfoVersion() {
		return $this->bookingInfoVersion;
	}

	/**
	 * 予約ステータス取得
	 * @return string 予約ステータス
	 */
	function getBookingStatus() {
		return $this->bookingStatus;
	}

	/**
	 * 実売予約日取得
	 * @return string 実売予約日
	 */
	function getBookingDate() {
		return $this->bookingDate;
	}

	/**
	 * 処理結果仕向先コード取得
	 * @return string 処理結果仕向先
	 */
	function getResultForward() {
		return $this->resultForward;
	}

	/**
	 * 処理結果承認番号取得
	 * @return string 処理結果承認番号
	 */
	function getResultApprove() {
		return $this->resultApprove;
	}

	/**
	 * 処理結果トランザクションID取得
	 * @return string 処理結果トランザクションID
	 */
	function getResultTranId() {
		return $this->resultTranId;
	}
	/**
	 * 処理結果決済日付取得
	 * @return string 処理結果決済日付
	 */
	function getResultTranDate() {
		return $this->resultTranDate;
	}
	/**
	 * 処理結果エラーコード取得
	 * @return string 処理結果エラーコード
	 */
	function getResultErrCode() {
		return $this->resultErrCode;
	}
	/**
	 * 処理結果エラー詳細コード取得
	 * @return string 処理結果エラー詳細コード
	 */
	function getResultErrInfo() {
		return $this->resultErrInfo;
	}

	/**
	 * 取引ID設定
	 *
	 * @param string $accessId 取引ID
	 */
	function setAccessId($accessId) {
		$this->accessId = $accessId;
	}

	/**
	 * 取引パスワード設定
	 *
	 * @param string $accessPass 取引パスワード
	 */
	function setAccessPass($accessPass) {
		$this->accessPass = $accessPass;
	}

	/**
	 * 予約情報バージョン設定
	 *
	 * @param string $bookingInfoVersion 予約情報バージョン
	 */
	function setBookingInfoVersion($bookingInfoVersion) {
		$this->bookingInfoVersion = $bookingInfoVersion;
	}

	/**
	 * 予約ステータス設定
	 *
	 * @param string $bookingStatus 予約ステータス
	 */
	function setBookingStatus($bookingStatus) {
		$this->bookingStatus = $bookingStatus;
	}
	/**
	 * 実売予約日設定
	 *
	 * @param string $bookingDate 実売予約日
	 */
	function setBookingDate($bookingDate) {
		$this->bookingDate = $bookingDate;
	}

	/**
	 * 処理結果仕向先コード設定
	 *
	 * @param string $resultForward 処理結果仕向先コード
	 */
	function setResultForward($resultForward) {
		$this->resultForward = $resultForward;
	}

	/**
	 * 処理結果承認番号設定
	 *
	 * @param string $resultApprove 処理結果承認番号
	 */
	function setResultApprove($resultApprove) {
		$this->resultApprove = $resultApprove;
	}

	/**
	 * 処理結果トランザクションID設定
	 *
	 * @param string $resultTranId 処理結果トランザクションID
	 */
	function setResultTranId($resultTranId) {
		$this->resultTranId = $resultTranId;
	}

	/**
	 * 処理結果決済日付設定
	 *
	 * @param string $resultTranDate 処理結果決済日付
	 */
	function setResultTranDate($resultTranDate) {
		$this->resultTranDate = $resultTranDate;
	}

	/**
	 * 処理結果エラーコード設定
	 *
	 * @param string $resultErrCode 処理結果エラーコード
	 */
	function setResultErrCode($resultErrCode) {
		$this->resultErrCode = $resultErrCode;
	}

	/**
	 * 処理結果エラー詳細コード設定
	 *
	 * @param string $resultErrInfo 処理結果エラー詳細コード
	 */
	function setResultErrInfo($resultErrInfo) {
		$this->resultErrInfo = $resultErrInfo;
	}


	/**
	 * 文字列表現
	 * <p>
	 *  現在の各パラメータを、パラメータ名=値&パラメータ名=値の形式で取得します。
	 * </p>
	 * @return string 出力パラメータの文字列表現
	 */
	function toString() {
	    $str  = 'AccessID=' . $this->getAccessId();
	    $str .= '&';
	    $str .= 'AccessPass=' . $this->getAccessPass();
	    $str .= '&';
	    $str .= 'BookingInfoVersion=' . $this->getBookingInfoVersion();
	    $str .= '&';
	    $str  = 'BookingStatus=' . $this->getBookingStatus();
	    $str .= '&';
	    $str  = 'BookingDate=' . $this->getBookingDate();
	    $str .= '&';
	    $str  = 'ResultForward=' . $this->getResultForward();
	    $str .= '&';
	    $str  = 'ResultApprove=' . $this->getResultApprove();
	    $str .= '&';
	    $str  = 'ResultTranId=' . $this->getResultTranId();
	    $str .= '&';
	    $str  = 'ResultTranDate=' . $this->getResultTranDate();
	    $str .= '&';
	    $str  = 'ResultErrCode=' . $this->getResultErrCode();
	    $str .= '&';
	    $str  = 'ResultErrInfo=' . $this->getResultErrInfo();

	    if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }

        return $str;
	}

}
?>