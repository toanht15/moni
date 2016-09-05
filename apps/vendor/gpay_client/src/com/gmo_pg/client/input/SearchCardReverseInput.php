<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');

/**
 * <b>逆引き会員ID検照会　入力パラメータクラス</b>
 * 
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 15-12-2014
 */
class SearchCardReverseInput extends BaseInput {

	/**
	 * @var string サイトID GMOPG発行のサイト識別ID
	 */
	var $siteId;

	/**
	 * @var string サイトパスワード
	 */
	var $sitePass;

	/**
	 * @var string カード番号
	 */
	var $cardNo;

	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function SearchCardReverseInput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function __construct($params = null) {
		parent::__construct($params);
	}

	
	
	/**
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteId() {
		return $this->siteId;
	}

	/**
	 * サイトパスワード取得
	 * @return string サイトパスワード
	 */
	function getSitePass() {
		return $this->sitePass;
	}

	/**
	 * カード番号取得
	 * @return string カード番号
	 */
	function getCardNo() {
		return $this->cardNo;
	}

	/**
	 * サイトID設定
	 *
	 * @param string $siteId サイトID
	 */
	function setSiteId($siteId) {
		$this->siteId = $siteId;
	}

	/**
	 * サイトパスワード設定
	 *
	 * @param string $sitePass サイトパスワード
	 */
	function setSitePass($sitePass) {
		$this->sitePass = $sitePass;
	}

	/**
	 * カード番号設定
	 *
	 * @param string $cardNo カード番号
	 */
	function setCardNo($cardNo) {
		$this->cardNo = $cardNo;
	}

	/**
	 * デフォルト値設定する
	 */
	function setDefaultValues() {
	}

	/**
	 * 入力パラメータ群の値を設定する
	 *
	 * @param IgnoreCaseMap $params 入力パラメータ
	 */
	function setInputValues($params) {
		// 入力パラメータがnullの場合は設定処理を行わない
	    if (is_null($params)) {
	        return;
	    }
	    
	    // 各項目の設定(CardSeqは値が数値でないものは無効とする)
	    $this->setSiteId($this->getStringValue($params, 'SiteID', $this->getSiteId()));
	    $this->setSitePass($this->getStringValue($params, 'SitePass', $this->getSitePass()));
        $this->setMemberId($this->getStringValue($params, 'CardNo', $this->getCardNo()));
        
	}

	/**
	 * 文字列表現
	 * URLのパラメータ文字列の形式の文字列を生成する
	 * @return string 接続文字列表現
	 */
	function toString() {
	    
	    $str  = 'SiteID=' . $this->encodeStr($this->getSiteId());
	    $str .= '&';
	    $str .= 'SitePass=' . $this->encodeStr($this->getSitePass());
	    $str .= '&';
	    $str .= 'CardNo=' . $this->encodeStr($this->getCardNo());
	    
	    return $str;   
	}

}
?>