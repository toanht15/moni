<?php
require_once ('com/gmo_pg/client/input/BaseInput.php');
require_once( 'com/gmo_pg/client/input/RedItemHolder.php');
/**
 * <b>不正防止実行　入力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage input
 * @see inputPackageInfo.php
 * @author GMO PaymentGateway
 */
class ExecFraudScreeningInput extends BaseInput {

	/**
	 * @var string ショップID
	 */
	var $shopID;
	/**
	 * @var string ショップパスワード
	 */
	var $shopPass;
	/**
	 * @var string 実行モード
	 */
	var $execMode;
	/**
	 * @var string 取引ID
	 */
	var $accessID;
	/**
	 * @var string 取引パスワード
	 */
	var $accessPass;
	/**
	 * @var bigDecimal 決済金額
	 */
	var $redAmt;
	/**
	 * @var string 通貨コード
	 */
	var $redCurrCd;
	/**
	 * @var string クレジットカード番号
	 */
	var $redAcctNum;
	/**
	 * @var string カード有効期限
	 */
	var $redCardExpDt;
	/**
	 * @var string 請求先情報有無判定フラグ
	 */
	var $redCustTypeCd;
	/**
	 * @var string ユーザID
	 */
	var $redCustId;
	/**
	 * @var string カード名義
	 */
	var $redCustFname;
	/**
	 * @var string 請求先顧客苗字
	 */
	var $redCustLname;
	/**
	 * @var string 請求先顧客住所１
	 */
	var $redCustAddr1;
	/**
	 * @var string 請求先顧客住所２
	 */
	var $redCustAddr2;
	/**
	 * @var string 請求先顧客住所３
	 */
	var $redCustAddr3;
	/**
	 * @var string 請求先顧客都道府県
	 */
	var $redCustCity;
	/**
	 * @var string 請求先顧客郵便番号
	 */
	var $redCustPostalCd;
	/**
	 * @var string 請求先顧客国
	 */
	var $redCustCntryCd;
	/**
	 * @var string 請求先顧客電話番号
	 */
	var $redCustHomePhone;
	/**
	 * @var string 請求先顧客メールアドレス
	 */
	var $redCustEmail;
	/**
	 * @var string エンドユーザIPアドレス
	 */
	var $redCustIpAddr;
	/**
	 * @var string リピータフラグ
	 */
	var $redEbtPrevcust;
	/**
	 * @var integer ユーザID登録後経過日数
	 */
	var $redEbtTof;
	/**
	 * @var string 発送先情報有無判定フラグ
	 */
	var $redShipTypeCd;
	/**
	 * @var string 発送先名前
	 */
	var $redShipFname;
	/**
	 * @var string 発送先苗字
	 */
	var $redShipLname;
	/**
	 * @var string 発送先住所１
	 */
	var $redShipAddr1;
	/**
	 * @var string 発送先住所２
	 */
	var $redShipAddr2;
	/**
	 * @var string 発送先住所３
	 */
	var $redShipAddr3;
	/**
	 * @var string 発送先都道府県
	 */
	var $redShipCity;
	/**
	 * @var string 発送先郵便番号
	 */
	var $redShipPostalCd;
	/**
	 * @var string 加盟店名
	 */
	var $redEmpCompany;
	/**
	 * @var string デバイス情報
	 */
	var $redEbtDeviceprint;
	/**
	 * @var string 予備項目8
	 */
	var $redEbtUserData8;
	/**
	 * @var string 予備項目9
	 */
	var $redEbtUserData9;
	/**
	 * @var integer 再購入日数
	 */
	var $redEbtUserData11;
	/**
	 * @var integer 過去購買回数
	 */
	var $redEbtUserData12;
	/**
	 * @var string 与信結果
	 */
	var $redEbtUserData13;
	/**
	 * @var string 郵便局・営業所留フラグ
	 */
	var $redEbtUserData15;
	/**
	 * @var string 郵便局・営業所名
	 */
	var $redEbtUserData16;
	/**
	 * @var integer ユーザポイント残高
	 */
	var $redEbtUserData17;
	/**
	 * @var integer カード登録変更回数
	 */
	var $redEbtUserData18;
	/**
	 * @var string 予備項目19
	 */
	var $redEbtUserData19;
	/**
	 * @var integer カードTOF
	 */
	var $redEbtUserData20;
	/**
	 * @var string 予備項目21
	 */
	var $redEbtUserData21;
	/**
	 * @var string 予備項目22
	 */
	var $redEbtUserData22;
	/**
	 * @var string 予備項目23
	 */
	var $redEbtUserData23;
	/**
	 * @var string 予備項目24
	 */
	var $redEbtUserData24;
	/**
	 * @var string 予備項目25
	 */
	var $redEbtUserData25;
	/**
	 * @var integer 繰り返し回数
	 */
	var $redOiRepeat;
	/**
	 * @var array 商品情報リスト
	 */
	var $redItemList;
	/**
	 * @var string 電文タイプ
	 */
	var $telegramType;


	/**
	 * コンストラクタ
	 *
	 * @param array $params 入力パラメータ
	 */
	function ExecFraudScreeningInput($params = null) {
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
	function getShopID() {
		return $this->shopID;
	}
	/**
	 * ショップパスワード取得
	 * @return string ショップパスワード
	 */
	function getShopPass() {
		return $this->shopPass;
	}
	/**
	 * 実行モード取得
	 * @return string 実行モード
	 */
	function getExecMode() {
		return $this->execMode;
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
	 * 決済金額取得
	 * @return bigDecimal 決済金額
	 */
	function getRedAmt() {
		return $this->redAmt;
	}
	/**
	 * 通貨コード取得
	 * @return string 通貨コード
	 */
	function getRedCurrCd() {
		return $this->redCurrCd;
	}
	/**
	 * クレジットカード番号取得
	 * @return string クレジットカード番号
	 */
	function getRedAcctNum() {
		return $this->redAcctNum;
	}
	/**
	 * カード有効期限取得
	 * @return string カード有効期限
	 */
	function getRedCardExpDt() {
		return $this->redCardExpDt;
	}
	/**
	 * 請求先情報有無判定フラグ取得
	 * @return string 請求先情報有無判定フラグ
	 */
	function getRedCustTypeCd() {
		return $this->redCustTypeCd;
	}
	/**
	 * ユーザID取得
	 * @return string ユーザID
	 */
	function getRedCustId() {
		return $this->redCustId;
	}
	/**
	 * カード名義取得
	 * @return string カード名義
	 */
	function getRedCustFname() {
		return $this->redCustFname;
	}
	/**
	 * 請求先顧客苗字取得
	 * @return string 請求先顧客苗字
	 */
	function getRedCustLname() {
		return $this->redCustLname;
	}
	/**
	 * 請求先顧客住所１取得
	 * @return string 請求先顧客住所１
	 */
	function getRedCustAddr1() {
		return $this->redCustAddr1;
	}
	/**
	 * 請求先顧客住所２取得
	 * @return string 請求先顧客住所２
	 */
	function getRedCustAddr2() {
		return $this->redCustAddr2;
	}
	/**
	 * 請求先顧客住所３取得
	 * @return string 請求先顧客住所３
	 */
	function getRedCustAddr3() {
		return $this->redCustAddr3;
	}
	/**
	 * 請求先顧客都道府県取得
	 * @return string 請求先顧客都道府県
	 */
	function getRedCustCity() {
		return $this->redCustCity;
	}
	/**
	 * 請求先顧客郵便番号取得
	 * @return string 請求先顧客郵便番号
	 */
	function getRedCustPostalCd() {
		return $this->redCustPostalCd;
	}
	/**
	 * 請求先顧客国取得
	 * @return string 請求先顧客国
	 */
	function getRedCustCntryCd() {
		return $this->redCustCntryCd;
	}
	/**
	 * 請求先顧客電話番号取得
	 * @return string 請求先顧客電話番号
	 */
	function getRedCustHomePhone() {
		return $this->redCustHomePhone;
	}
	/**
	 * 請求先顧客メールアドレス取得
	 * @return string 請求先顧客メールアドレス
	 */
	function getRedCustEmail() {
		return $this->redCustEmail;
	}
	/**
	 * エンドユーザIPアドレス取得
	 * @return string エンドユーザIPアドレス
	 */
	function getRedCustIpAddr() {
		return $this->redCustIpAddr;
	}
	/**
	 * リピータフラグ取得
	 * @return string リピータフラグ
	 */
	function getRedEbtPrevcust() {
		return $this->redEbtPrevcust;
	}
	/**
	 * ユーザID登録後経過日数取得
	 * @return integer ユーザID登録後経過日数
	 */
	function getRedEbtTof() {
		return $this->redEbtTof;
	}
	/**
	 * 発送先情報有無判定フラグ取得
	 * @return string 発送先情報有無判定フラグ
	 */
	function getRedShipTypeCd() {
		return $this->redShipTypeCd;
	}
	/**
	 * 発送先名前取得
	 * @return string 発送先名前
	 */
	function getRedShipFname() {
		return $this->redShipFname;
	}
	/**
	 * 発送先苗字取得
	 * @return string 発送先苗字
	 */
	function getRedShipLname() {
		return $this->redShipLname;
	}
	/**
	 * 発送先住所１取得
	 * @return string 発送先住所１
	 */
	function getRedShipAddr1() {
		return $this->redShipAddr1;
	}
	/**
	 * 発送先住所２取得
	 * @return string 発送先住所２
	 */
	function getRedShipAddr2() {
		return $this->redShipAddr2;
	}
	/**
	 * 発送先住所３取得
	 * @return string 発送先住所３
	 */
	function getRedShipAddr3() {
		return $this->redShipAddr3;
	}
	/**
	 * 発送先都道府県取得
	 * @return string 発送先都道府県
	 */
	function getRedShipCity() {
		return $this->redShipCity;
	}
	/**
	 * 発送先郵便番号取得
	 * @return string 発送先郵便番号
	 */
	function getRedShipPostalCd() {
		return $this->redShipPostalCd;
	}
	/**
	 * 加盟店名取得
	 * @return string 加盟店名
	 */
	function getRedEmpCompany() {
		return $this->redEmpCompany;
	}
	/**
	 * デバイス情報取得
	 * @return string デバイス情報
	 */
	function getRedEbtDeviceprint() {
		return $this->redEbtDeviceprint;
	}
	/**
	 * 予備項目8取得
	 * @return string 予備項目8
	 */
	function getRedEbtUserData8() {
		return $this->redEbtUserData8;
	}
	/**
	 * 予備項目9取得
	 * @return string 予備項目9
	 */
	function getRedEbtUserData9() {
		return $this->redEbtUserData9;
	}
	/**
	 * 再購入日数取得
	 * @return integer 再購入日数
	 */
	function getRedEbtUserData11() {
		return $this->redEbtUserData11;
	}
	/**
	 * 過去購買回数取得
	 * @return integer 過去購買回数
	 */
	function getRedEbtUserData12() {
		return $this->redEbtUserData12;
	}
	/**
	 * 与信結果取得
	 * @return string 与信結果
	 */
	function getRedEbtUserData13() {
		return $this->redEbtUserData13;
	}
	/**
	 * 郵便局・営業所留フラグ取得
	 * @return string 郵便局・営業所留フラグ
	 */
	function getRedEbtUserData15() {
		return $this->redEbtUserData15;
	}
	/**
	 * 郵便局・営業所名取得
	 * @return string 郵便局・営業所名
	 */
	function getRedEbtUserData16() {
		return $this->redEbtUserData16;
	}
	/**
	 * ユーザポイント残高取得
	 * @return integer ユーザポイント残高
	 */
	function getRedEbtUserData17() {
		return $this->redEbtUserData17;
	}
	/**
	 * カード登録変更回数取得
	 * @return integer カード登録変更回数
	 */
	function getRedEbtUserData18() {
		return $this->redEbtUserData18;
	}
	/**
	 * 予備項目19取得
	 * @return string 予備項目19
	 */
	function getRedEbtUserData19() {
		return $this->redEbtUserData19;
	}
	/**
	 * カードTOF取得
	 * @return integer カードTOF
	 */
	function getRedEbtUserData20() {
		return $this->redEbtUserData20;
	}
	/**
	 * 予備項目21取得
	 * @return string 予備項目21
	 */
	function getRedEbtUserData21() {
		return $this->redEbtUserData21;
	}
	/**
	 * 予備項目22取得
	 * @return string 予備項目22
	 */
	function getRedEbtUserData22() {
		return $this->redEbtUserData22;
	}
	/**
	 * 予備項目23取得
	 * @return string 予備項目23
	 */
	function getRedEbtUserData23() {
		return $this->redEbtUserData23;
	}
	/**
	 * 予備項目24取得
	 * @return string 予備項目24
	 */
	function getRedEbtUserData24() {
		return $this->redEbtUserData24;
	}
	/**
	 * 予備項目25取得
	 * @return string 予備項目25
	 */
	function getRedEbtUserData25() {
		return $this->redEbtUserData25;
	}
	/**
	 * 繰り返し回数取得
	 * @return integer 繰り返し回数
	 */
	function getRedOiRepeat() {
		return $this->redOiRepeat;
	}
	/**
	 * 商品情報リスト取得
	 * @return array 商品情報リスト
	 */
	function getRedItemList() {
		return $this->redItemList;
	}
	/**
	 * 電文タイプ取得
	 * @return string 電文タイプ
	 */
	function getTelegramType() {
		return $this->telegramType;
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
	 * ショップパスワード設定
	 *
	 * @param string $shopPass
	 */
	function setShopPass($shopPass) {
		$this->shopPass = $shopPass;
	}
	/**
	 * 実行モード設定
	 *
	 * @param string $execMode
	 */
	function setExecMode($execMode) {
		$this->execMode = $execMode;
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
	 * 決済金額設定
	 *
	 * @param bigDecimal $redAmt
	 */
	function setRedAmt($redAmt) {
		$this->redAmt = $redAmt;
	}
	/**
	 * 通貨コード設定
	 *
	 * @param string $redCurrCd
	 */
	function setRedCurrCd($redCurrCd) {
		$this->redCurrCd = $redCurrCd;
	}
	/**
	 * クレジットカード番号設定
	 *
	 * @param string $redAcctNum
	 */
	function setRedAcctNum($redAcctNum) {
		$this->redAcctNum = $redAcctNum;
	}
	/**
	 * カード有効期限設定
	 *
	 * @param string $redCardExpDt
	 */
	function setRedCardExpDt($redCardExpDt) {
		$this->redCardExpDt = $redCardExpDt;
	}
	/**
	 * 請求先情報有無判定フラグ設定
	 *
	 * @param string $redCustTypeCd
	 */
	function setRedCustTypeCd($redCustTypeCd) {
		$this->redCustTypeCd = $redCustTypeCd;
	}
	/**
	 * ユーザID設定
	 *
	 * @param string $redCustId
	 */
	function setRedCustId($redCustId) {
		$this->redCustId = $redCustId;
	}
	/**
	 * カード名義設定
	 *
	 * @param string $redCustFname
	 */
	function setRedCustFname($redCustFname) {
		$this->redCustFname = $redCustFname;
	}
	/**
	 * 請求先顧客苗字設定
	 *
	 * @param string $redCustLname
	 */
	function setRedCustLname($redCustLname) {
		$this->redCustLname = $redCustLname;
	}
	/**
	 * 請求先顧客住所１設定
	 *
	 * @param string $redCustAddr1
	 */
	function setRedCustAddr1($redCustAddr1) {
		$this->redCustAddr1 = $redCustAddr1;
	}
	/**
	 * 請求先顧客住所２設定
	 *
	 * @param string $redCustAddr2
	 */
	function setRedCustAddr2($redCustAddr2) {
		$this->redCustAddr2 = $redCustAddr2;
	}
	/**
	 * 請求先顧客住所３設定
	 *
	 * @param string $redCustAddr3
	 */
	function setRedCustAddr3($redCustAddr3) {
		$this->redCustAddr3 = $redCustAddr3;
	}
	/**
	 * 請求先顧客都道府県設定
	 *
	 * @param string $redCustCity
	 */
	function setRedCustCity($redCustCity) {
		$this->redCustCity = $redCustCity;
	}
	/**
	 * 請求先顧客郵便番号設定
	 *
	 * @param string $redCustPostalCd
	 */
	function setRedCustPostalCd($redCustPostalCd) {
		$this->redCustPostalCd = $redCustPostalCd;
	}
	/**
	 * 請求先顧客国設定
	 *
	 * @param string $redCustCntryCd
	 */
	function setRedCustCntryCd($redCustCntryCd) {
		$this->redCustCntryCd = $redCustCntryCd;
	}
	/**
	 * 請求先顧客電話番号設定
	 *
	 * @param string $redCustHomePhone
	 */
	function setRedCustHomePhone($redCustHomePhone) {
		$this->redCustHomePhone = $redCustHomePhone;
	}
	/**
	 * 請求先顧客メールアドレス設定
	 *
	 * @param string $redCustEmail
	 */
	function setRedCustEmail($redCustEmail) {
		$this->redCustEmail = $redCustEmail;
	}
	/**
	 * エンドユーザIPアドレス設定
	 *
	 * @param string $redCustIpAddr
	 */
	function setRedCustIpAddr($redCustIpAddr) {
		$this->redCustIpAddr = $redCustIpAddr;
	}
	/**
	 * リピータフラグ設定
	 *
	 * @param string $redEbtPrevcust
	 */
	function setRedEbtPrevcust($redEbtPrevcust) {
		$this->redEbtPrevcust = $redEbtPrevcust;
	}
	/**
	 * ユーザID登録後経過日数設定
	 *
	 * @param integer $redEbtTof
	 */
	function setRedEbtTof($redEbtTof) {
		$this->redEbtTof = $redEbtTof;
	}
	/**
	 * 発送先情報有無判定フラグ設定
	 *
	 * @param string $redShipTypeCd
	 */
	function setRedShipTypeCd($redShipTypeCd) {
		$this->redShipTypeCd = $redShipTypeCd;
	}
	/**
	 * 発送先名前設定
	 *
	 * @param string $redShipFname
	 */
	function setRedShipFname($redShipFname) {
		$this->redShipFname = $redShipFname;
	}
	/**
	 * 発送先苗字設定
	 *
	 * @param string $redShipLname
	 */
	function setRedShipLname($redShipLname) {
		$this->redShipLname = $redShipLname;
	}
	/**
	 * 発送先住所１設定
	 *
	 * @param string $redShipAddr1
	 */
	function setRedShipAddr1($redShipAddr1) {
		$this->redShipAddr1 = $redShipAddr1;
	}
	/**
	 * 発送先住所２設定
	 *
	 * @param string $redShipAddr2
	 */
	function setRedShipAddr2($redShipAddr2) {
		$this->redShipAddr2 = $redShipAddr2;
	}
	/**
	 * 発送先住所３設定
	 *
	 * @param string $redShipAddr3
	 */
	function setRedShipAddr3($redShipAddr3) {
		$this->redShipAddr3 = $redShipAddr3;
	}
	/**
	 * 発送先都道府県設定
	 *
	 * @param string $redShipCity
	 */
	function setRedShipCity($redShipCity) {
		$this->redShipCity = $redShipCity;
	}
	/**
	 * 発送先郵便番号設定
	 *
	 * @param string $redShipPostalCd
	 */
	function setRedShipPostalCd($redShipPostalCd) {
		$this->redShipPostalCd = $redShipPostalCd;
	}
	/**
	 * 加盟店名設定
	 *
	 * @param string $redEmpCompany
	 */
	function setRedEmpCompany($redEmpCompany) {
		$this->redEmpCompany = $redEmpCompany;
	}
	/**
	 * デバイス情報設定
	 *
	 * @param string $redEbtDeviceprint
	 */
	function setRedEbtDeviceprint($redEbtDeviceprint) {
		$this->redEbtDeviceprint = $redEbtDeviceprint;
	}
	/**
	 * 予備項目8設定
	 *
	 * @param string $redEbtUserData8
	 */
	function setRedEbtUserData8($redEbtUserData8) {
		$this->redEbtUserData8 = $redEbtUserData8;
	}
	/**
	 * 予備項目9設定
	 *
	 * @param string $redEbtUserData9
	 */
	function setRedEbtUserData9($redEbtUserData9) {
		$this->redEbtUserData9 = $redEbtUserData9;
	}
	/**
	 * 再購入日数設定
	 *
	 * @param integer $redEbtUserData11
	 */
	function setRedEbtUserData11($redEbtUserData11) {
		$this->redEbtUserData11 = $redEbtUserData11;
	}
	/**
	 * 過去購買回数設定
	 *
	 * @param integer $redEbtUserData12
	 */
	function setRedEbtUserData12($redEbtUserData12) {
		$this->redEbtUserData12 = $redEbtUserData12;
	}
	/**
	 * 与信結果設定
	 *
	 * @param string $redEbtUserData13
	 */
	function setRedEbtUserData13($redEbtUserData13) {
		$this->redEbtUserData13 = $redEbtUserData13;
	}
	/**
	 * 郵便局・営業所留フラグ設定
	 *
	 * @param string $redEbtUserData15
	 */
	function setRedEbtUserData15($redEbtUserData15) {
		$this->redEbtUserData15 = $redEbtUserData15;
	}
	/**
	 * 郵便局・営業所名設定
	 *
	 * @param string $redEbtUserData16
	 */
	function setRedEbtUserData16($redEbtUserData16) {
		$this->redEbtUserData16 = $redEbtUserData16;
	}
	/**
	 * ユーザポイント残高設定
	 *
	 * @param integer $redEbtUserData17
	 */
	function setRedEbtUserData17($redEbtUserData17) {
		$this->redEbtUserData17 = $redEbtUserData17;
	}
	/**
	 * カード登録変更回数設定
	 *
	 * @param integer $redEbtUserData18
	 */
	function setRedEbtUserData18($redEbtUserData18) {
		$this->redEbtUserData18 = $redEbtUserData18;
	}
	/**
	 * 予備項目19設定
	 *
	 * @param string $redEbtUserData19
	 */
	function setRedEbtUserData19($redEbtUserData19) {
		$this->redEbtUserData19 = $redEbtUserData19;
	}
	/**
	 * カードTOF設定
	 *
	 * @param integer $redEbtUserData20
	 */
	function setRedEbtUserData20($redEbtUserData20) {
		$this->redEbtUserData20 = $redEbtUserData20;
	}
	/**
	 * 予備項目21設定
	 *
	 * @param string $redEbtUserData21
	 */
	function setRedEbtUserData21($redEbtUserData21) {
		$this->redEbtUserData21 = $redEbtUserData21;
	}
	/**
	 * 予備項目22設定
	 *
	 * @param string $redEbtUserData22
	 */
	function setRedEbtUserData22($redEbtUserData22) {
		$this->redEbtUserData22 = $redEbtUserData22;
	}
	/**
	 * 予備項目23設定
	 *
	 * @param string $redEbtUserData23
	 */
	function setRedEbtUserData23($redEbtUserData23) {
		$this->redEbtUserData23 = $redEbtUserData23;
	}
	/**
	 * 予備項目24設定
	 *
	 * @param string $redEbtUserData24
	 */
	function setRedEbtUserData24($redEbtUserData24) {
		$this->redEbtUserData24 = $redEbtUserData24;
	}
	/**
	 * 予備項目25設定
	 *
	 * @param string $redEbtUserData25
	 */
	function setRedEbtUserData25($redEbtUserData25) {
		$this->redEbtUserData25 = $redEbtUserData25;
	}
	/**
	 * 商品情報リスト設定
	 *
	 * @param array $redItemList
	 */
	function setRedItemList($redItemList) {
		if (is_array($redItemList) && count($redItemList) > 0){
			$this->redItemList = $redItemList;
			$this->redOiRepeat = count($redItemList);
		} else {
			$this->redItemList = array();
		}
	}
	/**
	 * 電文タイプ設定
	 *
	 * @param string $telegramType
	 */
	function setTelegramType($telegramType) {
		$this->telegramType = $telegramType;
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

		$this->setShopID($this->getStringValue($params, 'ShopID', $this->getShopID()));
		$this->setShopPass($this->getStringValue($params, 'ShopPass', $this->getShopPass()));
		$this->setExecMode($this->getStringValue($params, 'ExecMode', $this->getExecMode()));
		$this->setAccessID($this->getStringValue($params, 'AccessID', $this->getAccessID()));
		$this->setAccessPass($this->getStringValue($params, 'AccessPass', $this->getAccessPass()));
		$this->setRedAmt($this->getStringValue($params, 'RedAmt', $this->getRedAmt()));
		$this->setRedCurrCd($this->getStringValue($params, 'RedCurrCd', $this->getRedCurrCd()));
		$this->setRedAcctNum($this->getStringValue($params, 'RedAcctNum', $this->getRedAcctNum()));
		$this->setRedCardExpDt($this->getStringValue($params, 'RedCardExpDt', $this->getRedCardExpDt()));
		$this->setRedCustTypeCd($this->getStringValue($params, 'RedCustTypeCd', $this->getRedCustTypeCd()));
		$this->setRedCustId($this->getStringValue($params, 'RedCustId', $this->getRedCustId()));
		$this->setRedCustFname($this->getStringValue($params, 'RedCustFname', $this->getRedCustFname()));
		$this->setRedCustLname($this->getStringValue($params, 'RedCustLname', $this->getRedCustLname()));
		$this->setRedCustAddr1($this->getStringValue($params, 'RedCustAddr1', $this->getRedCustAddr1()));
		$this->setRedCustAddr2($this->getStringValue($params, 'RedCustAddr2', $this->getRedCustAddr2()));
		$this->setRedCustAddr3($this->getStringValue($params, 'RedCustAddr3', $this->getRedCustAddr3()));
		$this->setRedCustCity($this->getStringValue($params, 'RedCustCity', $this->getRedCustCity()));
		$this->setRedCustPostalCd($this->getStringValue($params, 'RedCustPostalCd', $this->getRedCustPostalCd()));
		$this->setRedCustCntryCd($this->getStringValue($params, 'RedCustCntryCd', $this->getRedCustCntryCd()));
		$this->setRedCustHomePhone($this->getStringValue($params, 'RedCustHomePhone', $this->getRedCustHomePhone()));
		$this->setRedCustEmail($this->getStringValue($params, 'RedCustEmail', $this->getRedCustEmail()));
		$this->setRedCustIpAddr($this->getStringValue($params, 'RedCustIpAddr', $this->getRedCustIpAddr()));
		$this->setRedEbtPrevcust($this->getStringValue($params, 'RedEbtPrevcust', $this->getRedEbtPrevcust()));
		$this->setRedEbtTof($this->getStringValue($params, 'RedEbtTof', $this->getRedEbtTof()));
		$this->setRedShipTypeCd($this->getStringValue($params, 'RedShipTypeCd', $this->getRedShipTypeCd()));
		$this->setRedShipFname($this->getStringValue($params, 'RedShipFname', $this->getRedShipFname()));
		$this->setRedShipLname($this->getStringValue($params, 'RedShipLname', $this->getRedShipLname()));
		$this->setRedShipAddr1($this->getStringValue($params, 'RedShipAddr1', $this->getRedShipAddr1()));
		$this->setRedShipAddr2($this->getStringValue($params, 'RedShipAddr2', $this->getRedShipAddr2()));
		$this->setRedShipAddr3($this->getStringValue($params, 'RedShipAddr3', $this->getRedShipAddr3()));
		$this->setRedShipCity($this->getStringValue($params, 'RedShipCity', $this->getRedShipCity()));
		$this->setRedShipPostalCd($this->getStringValue($params, 'RedShipPostalCd', $this->getRedShipPostalCd()));
		$this->setRedEmpCompany($this->getStringValue($params, 'RedEmpCompany', $this->getRedEmpCompany()));
		$this->setRedEbtDeviceprint($this->getStringValue($params, 'RedEbtDeviceprint', $this->getRedEbtDeviceprint()));
		$this->setRedEbtUserData8($this->getStringValue($params, 'RedEbtUserData8', $this->getRedEbtUserData8()));
		$this->setRedEbtUserData9($this->getStringValue($params, 'RedEbtUserData9', $this->getRedEbtUserData9()));
		$this->setRedEbtUserData11($this->getStringValue($params, 'RedEbtUserData11', $this->getRedEbtUserData11()));
		$this->setRedEbtUserData12($this->getStringValue($params, 'RedEbtUserData12', $this->getRedEbtUserData12()));
		$this->setRedEbtUserData13($this->getStringValue($params, 'RedEbtUserData13', $this->getRedEbtUserData13()));
		$this->setRedEbtUserData15($this->getStringValue($params, 'RedEbtUserData15', $this->getRedEbtUserData15()));
		$this->setRedEbtUserData16($this->getStringValue($params, 'RedEbtUserData16', $this->getRedEbtUserData16()));
		$this->setRedEbtUserData17($this->getStringValue($params, 'RedEbtUserData17', $this->getRedEbtUserData17()));
		$this->setRedEbtUserData18($this->getStringValue($params, 'RedEbtUserData18', $this->getRedEbtUserData18()));
		$this->setRedEbtUserData19($this->getStringValue($params, 'RedEbtUserData19', $this->getRedEbtUserData19()));
		$this->setRedEbtUserData20($this->getStringValue($params, 'RedEbtUserData20', $this->getRedEbtUserData20()));
		$this->setRedEbtUserData21($this->getStringValue($params, 'RedEbtUserData21', $this->getRedEbtUserData21()));
		$this->setRedEbtUserData22($this->getStringValue($params, 'RedEbtUserData22', $this->getRedEbtUserData22()));
		$this->setRedEbtUserData23($this->getStringValue($params, 'RedEbtUserData23', $this->getRedEbtUserData23()));
		$this->setRedEbtUserData24($this->getStringValue($params, 'RedEbtUserData24', $this->getRedEbtUserData24()));
		$this->setRedEbtUserData25($this->getStringValue($params, 'RedEbtUserData25', $this->getRedEbtUserData25()));
		$this->setRedItemList($this->getStringValue($params, 'RedItemList', $this->getRedItemList()));
		$this->setTelegramType($this->getStringValue($params, 'TelegramType', $this->getTelegramType()));

	}

	/**
	 * 文字列表現
	 * @return string 接続文字列表現
	 */
	function toString() {
		$str ='';
		$str .= 'ShopID=' . $this->encodeStr($this->getShopID());
		$str .='&';
		$str .= 'ShopPass=' . $this->encodeStr($this->getShopPass());
		$str .='&';
		$str .= 'ExecMode=' . $this->encodeStr($this->getExecMode());
		$str .='&';
		$str .= 'AccessID=' . $this->encodeStr($this->getAccessID());
		$str .='&';
		$str .= 'AccessPass=' . $this->encodeStr($this->getAccessPass());
		$str .='&';
		$str .= 'RED_AMT=' . $this->encodeStr($this->getRedAmt());
		$str .='&';
		$str .= 'RED_CURR_CD=' . $this->encodeStr($this->getRedCurrCd());
		$str .='&';
		$str .= 'RED_ACCT_NUM=' . $this->encodeStr($this->getRedAcctNum());
		$str .='&';
		$str .= 'RED_CARD_EXP_DT=' . $this->encodeStr($this->getRedCardExpDt());
		$str .='&';
		$str .= 'RED_CUST_TYPE_CD=' . $this->encodeStr($this->getRedCustTypeCd());
		$str .='&';
		$str .= 'RED_CUST_ID=' . $this->encodeStr($this->getRedCustId());
		$str .='&';
		$str .= 'RED_CUST_FNAME=' . $this->encodeStr($this->getRedCustFname());
		$str .='&';
		$str .= 'RED_CUST_LNAME=' . $this->encodeStr($this->getRedCustLname());
		$str .='&';
		$str .= 'RED_CUST_ADDR1=' . $this->encodeStr($this->getRedCustAddr1());
		$str .='&';
		$str .= 'RED_CUST_ADDR2=' . $this->encodeStr($this->getRedCustAddr2());
		$str .='&';
		$str .= 'RED_CUST_ADDR3=' . $this->encodeStr($this->getRedCustAddr3());
		$str .='&';
		$str .= 'RED_CUST_CITY=' . $this->encodeStr($this->getRedCustCity());
		$str .='&';
		$str .= 'RED_CUST_POSTAL_CD=' . $this->encodeStr($this->getRedCustPostalCd());
		$str .='&';
		$str .= 'RED_CUST_CNTRY_CD=' . $this->encodeStr($this->getRedCustCntryCd());
		$str .='&';
		$str .= 'RED_CUST_HOME_PHONE=' . $this->encodeStr($this->getRedCustHomePhone());
		$str .='&';
		$str .= 'RED_CUST_EMAIL=' . $this->encodeStr($this->getRedCustEmail());
		$str .='&';
		$str .= 'RED_CUST_IP_ADDR=' . $this->encodeStr($this->getRedCustIpAddr());
		$str .='&';
		$str .= 'RED_EBT_PREVCUST=' . $this->encodeStr($this->getRedEbtPrevcust());
		$str .='&';
		$str .= 'RED_EBT_TOF=' . $this->encodeStr($this->getRedEbtTof());
		$str .='&';
		$str .= 'RED_SHIP_TYPE_CD=' . $this->encodeStr($this->getRedShipTypeCd());
		$str .='&';
		$str .= 'RED_SHIP_FNAME=' . $this->encodeStr($this->getRedShipFname());
		$str .='&';
		$str .= 'RED_SHIP_LNAME=' . $this->encodeStr($this->getRedShipLname());
		$str .='&';
		$str .= 'RED_SHIP_ADDR1=' . $this->encodeStr($this->getRedShipAddr1());
		$str .='&';
		$str .= 'RED_SHIP_ADDR2=' . $this->encodeStr($this->getRedShipAddr2());
		$str .='&';
		$str .= 'RED_SHIP_ADDR3=' . $this->encodeStr($this->getRedShipAddr3());
		$str .='&';
		$str .= 'RED_SHIP_CITY=' . $this->encodeStr($this->getRedShipCity());
		$str .='&';
		$str .= 'RED_SHIP_POSTAL_CD=' . $this->encodeStr($this->getRedShipPostalCd());
		$str .='&';
		$str .= 'RED_EMP_COMPANY=' . $this->encodeStr($this->getRedEmpCompany());
		$str .='&';
		$str .= 'RED_EBT_DEVICEPRINT=' . $this->encodeStr($this->getRedEbtDeviceprint());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA8=' . $this->encodeStr($this->getRedEbtUserData8());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA9=' . $this->encodeStr($this->getRedEbtUserData9());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA11=' . $this->encodeStr($this->getRedEbtUserData11());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA12=' . $this->encodeStr($this->getRedEbtUserData12());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA13=' . $this->encodeStr($this->getRedEbtUserData13());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA15=' . $this->encodeStr($this->getRedEbtUserData15());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA16=' . $this->encodeStr($this->getRedEbtUserData16());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA17=' . $this->encodeStr($this->getRedEbtUserData17());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA18=' . $this->encodeStr($this->getRedEbtUserData18());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA19=' . $this->encodeStr($this->getRedEbtUserData19());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA20=' . $this->encodeStr($this->getRedEbtUserData20());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA21=' . $this->encodeStr($this->getRedEbtUserData21());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA22=' . $this->encodeStr($this->getRedEbtUserData22());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA23=' . $this->encodeStr($this->getRedEbtUserData23());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA24=' . $this->encodeStr($this->getRedEbtUserData24());
		$str .='&';
		$str .= 'RED_EBT_USER_DATA25=' . $this->encodeStr($this->getRedEbtUserData25());
		$str .='&';
		$str .= 'RED_OI_REPEAT=' . $this->encodeStr($this->getRedOiRepeat());

		// 商品情報の入力パラメータを設定
		$count = 1;
		foreach( $this->redItemList as $redItemHolder ){

			$str .='&';
			$str .= 'RED_ITEM_QTY' . $count . '=' . $this->encodeStr($redItemHolder->getRedItemQty());
			$str .='&';
			$str .= 'RED_ITEM_PROD_CD' . $count . '=' . $this->encodeStr($redItemHolder->getRedItemProdCd());
			$str .='&';
			$str .= 'RED_ITEM_MAN_PART_NO' . $count . '=' . $this->encodeStr($redItemHolder->getRedItemManPartNo());
			$str .='&';
			$str .= 'RED_ITEM_DESC' . $count . '=' . $this->encodeStr($redItemHolder->getRedItemDesc());
			$str .='&';
			$str .= 'RED_EBT_ITEM_CST' . $count . '=' . $this->encodeStr($redItemHolder->getRedEbtItemCst());
			$str .='&';
			$str .= 'RED_ITEM_GIFT_MSG' . $count . '=' . $this->encodeStr($redItemHolder->getRedItemGiftMsg());

			$count++;
		}

		$str .='&';
		$str .= 'TelegramType=' . $this->encodeStr($this->getTelegramType());

		return $str;
	}


}
?>
