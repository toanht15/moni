<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
/**
 * <b>実売予約キャンセル　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 10-22-2012 00:00:00
 */
class UnbookSalesProcessInput extends BaseInput {

	/**
	 * @var string GMO-PGが発行する、PGカード決済サービス中で加盟店様を識別するID
	 */
	var $shopId;

	/**
	 * @var string ショップIDと対になるパスワード
	 */
	var $shopPass;

	/**
	 * @var string 取引ID。GMO-PGが払い出した、取引を特定するID
	 */
	var $accessId;

	/**
	 * @var string 取引パスワード。取引IDと対になるパスワード
	 */
	var $accessPass;


	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function UnbookSalesProcessInput($params = null) {
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
	 * ショップID取得
	 * @return string ショップID
	 */
	function getShopId() {
		return $this->shopId;
	}

	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->shopPass;
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
	 * ショップID設定
	 *
	 * @param string $shopId
	 */
	function setShopId($shopId) {
		$this->shopId = $shopId;
	}

	/**
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->shopPass = $shopPass;
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
	 * 取引パスワードを設定
	 *
	 * @param string $accessPass 取引パスワード
	 */
	function setAccessPass($accessPass) {
		$this->accessPass = $accessPass;
	}

	/**
	 * デフォルト値設定
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

	    // 各項目の設定(Amount,Taxは値が数値でないものは無効とする)
	    $this->setShopId($this->getStringValue($params, 'ShopID', $this->getShopId()));
	    $this->setShopPass($this->getStringValue($params, 'ShopPass', $this->getShopPass()));
        $this->setAccessId($this->getStringValue($params, 'AccessID', $this->getAccessId()));
	    $this->setAccessPass($this->getStringValue($params, 'AccessPass', $this->getAccessPass()));
	}

	/**
	 * 文字列表現
	 * @return string 接続文字列表現
	 */
	function toString() {

	    $str  = 'ShopID=' . $this->encodeStr($this->getShopId());
	    $str .= '&';
	    $str .= 'ShopPass=' . $this->encodeStr($this->getShopPass());
	    $str .= '&';
	    $str .= 'AccessID=' . $this->encodeStr($this->getAccessId());
	    $str .= '&';
	    $str .= 'AccessPass=' . $this->encodeStr($this->getAccessPass());

	    return $str;
	}


}
?>