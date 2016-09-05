<?php
require_once ('com/gmo_pg/client/output/BaseOutput.php');
require_once ('com/gmo_pg/client/common/Const.php');
/**
 * <b>取引照会マルチ　出力パラメータクラス</b>
 *
 * @package com.gmo_pg.client
 * @subpackage output
 * @see outputPackageInfo.php
 * @author GMO PaymentGateway
 */
class SearchTradeMultiOutput extends BaseOutput {

	/**
	 * @var string オーダーID
	 */
	var $orderId;

	/**
	 * @var string 取引ステータス
	 */
	var $status;

	/**
	 * @var string 処理日時
	 */
	var $processDate;

	/**
	 * @var string 処理区分
	 */
	var $jobCd;

	/**
	 * @var string 取引ID
	 */
	var $accessId;

	/**
	 * @var string 取引パスワード
	 */
	var $accessPass;

	/**
	 * @var  string 商品コード
	 */
	var $itemCode;

	/**
	 * @var string 通貨コード
	 */
	var $currency;

	/**
	 * @var integer 利用金額
	 */
	var $amount;

	/**
	 * @var integer 税送料
	 */
	var $tax;

	/**
	 * @var BigDecimal 利用金額
	 */
	var $amountBigDecimal;

	/**
	 * @var BigDecimal 税送料
	 */
	var $taxBigDecimal;

	/**
	 * @var string サイトID
	 */
	var $siteId;

	/**
	 * @var string 会員ID
	 */
	var $memberId;

	/**
	 * @var string カード番号
	 */
	var $cardNo;

	/**
	 * @var string カード有効期限
	 */
	var $expire;

	/**
	 * @var string 支払い方法
	 */
	var $method;

	/**
	 * @var integer 支払回数
	 */
	var $payTimes;

	/**
	 * @var string 仕向先コード
	 */
	var $forward;

	/**
	 * @var string トランザクションID
	 */
	var $transactionId;

	/**
	 * @var string 承認番号
	 */
	var $approve;

	/**
	 * @var string 加盟店自由項目1
	 */
	var $clientField1;

	/**
	 * @var string 加盟店自由項目2
	 */
	var $clientField2;

	/**
	 * @var string 加盟店自由項目3
	 */
	var $clientField3;

	/**
	 * 決済方法
	 * 0：クレジット
	 * 1：モバイルSuica
	 * 2：モバイルEdy
	 * 3：コンビニ
	 * 4：Pay-easy
	 * 5：Paypal
	 * 7：Webmoney
	 * 8：au簡単決済
	 * 9：ドコモケータイ払い
	 * 10：ドコモ継続決済
	 * 11：ソフトバンクケータイ支払い決済
	 * 12：じぶん銀行決済
	 * 13：au継続課金
	 * 14：JCBプリカ
	 * 16：NET CASH
	 * 18：楽天ID
	 * 19：多通貨クレジットカード
	 * 20：LINE Pay
	 * 21：ネット銀聯
	 * 22：ソフトバンク継続
	 * 23：銀行振込(バーチャル口座)
	 * 24：リクルートかんたん支払い
	 * 25：リクルートかんたん支払い継続課金
	 * 26：自動売上

	 *
	 * @var string
	 */
	var $payType;

	var $cvsCode;
	var $cvsConfNo;
	var $cvsReceiptNo;
	var $edyReceiptNo;
	var $edyOrderNo;
	var $suicaReceiptNo;
	var $suicaOrderNo;
	var $custId;
	var $bkCode;
	var $confNo;
	var $paymentTerm;
	var $encryptReceiptNo;

	/**
	 * @var string WebMoney管理番号
	 */
	var $webmoneyMangementNo;

	/**
	 * @var string WebMoney決済コード
	 */
	var $webmoneySettleCode;

	/**
	 * @var string auかんたん決済決済情報番号
	 */
	var $auPayInfoNo;

	/**
	 * @var string auかんたん決済支払方法
	 */
	var $auPayMethod;

	/**
	 * @var string auかんたん決済キャンセル金額
	 */
	var $auCancelAmount;

	/**
	 * @var string auかんたん決済キャンセル税送料
	 */
	var $auCancelTax;

	/**
	 * @var string ドコモ決済番号
	 */
	var $docomoSettlementCode;

	/**
	 * @var string ドコモキャンセル金額
	 */
	var $docomoCancelAmount;

	/**
	 * @var string ドコモキャンセル税送料
	 */
	var $docomoCancelTax;

	/**
	 * @var string ソフトバンク処理トラッキングID
	 */
	var $sbTrackingId;

	/**
	 * @var integer ソフトバンクキャンセル金額
	 */
	var $sbCancelAmount;

	/**
	 * @var integer ソフトバンクキャンセル税送料
	 */
	var $sbCancelTax;

	/**
	 * @var じぶん銀行受付番号
	 */
	var $jibunReceiptNo;

	/**
	 * @var じぶん銀行受付番号
	 */

	/**
	 * @var integer au継続 初回課金利用金額
	 */
	var $firstAmount;
	/**
	 * @var integer au継続 初回課金税送料
	 */
	var $firstTax;
	/**
	 * @var string au継続 課金タイミング区分
	 */
	var $accountTimingKbn;
	/**
	 * @var string au継続 課金タイミング
	 */
	var $accountTiming;
	/**
	 * @var string au継続 初回課金日
	 */
	var $firstAccountDate;
	/**
	 * @var string au継続 エラーコード
	 */
	var $auContinuaceErrCode;
	/**
	 * @var string au継続 エラー詳細
	 */
	var $auContinuaceErrInfo;
	/**
	 * @var string au継続 au継続課金ID
	 */
	var $auContinueAccountId;

	/**
	 * @var string JcbPreca 伝票番号
	 */
	var $jcbPrecaSalesCode;
	/**
	 * @var string Netcash NET CASH決済方法
	 */
	var $netCashPayType;
	/**
	 * @var string RakutenId 注文日
	 */
	var $orderDate;
	/**
	 * @var string RakutenId 完了日
	 */
	var $completionDate;
	/**
	 * @var integer RakutenId クーポン金額
	 */
	var $rakutenidCouponFee;
	/**
	 * @var string Linepay LINE Pay商品名
	 */
	var $linepayProductName;
	/**
	 * @var bigDecimal Linepay LINE Payキャンセル金額
	 */
	var $linepayCancelAmount;
	/**
	 * @var bigDecimal Linepay LINE Payキャンセル税送料
	 */
	var $linepayCancelTax;
	/**
	 * @var string Linepay LINE Pay支払手段
	 */
	var $linepayPayMethod;
	/**
	 * @var string Unionpay 商品名
	 */
	var $commodityName;
	/**
	 * @var string SbContinuance 課金開始月
	 */
	var $sbStartChargeMonth;
	/**
	 * @var bigDecimal Virtualaccount 振込要求金額
	 */
	var $vaRequestAmount;
	/**
	 * @var string Virtualaccount 取引有効期限
	 */
	var $vaExpireDate;
	/**
	 * @var string Virtualaccount 取引事由
	 */
	var $vaTradeReason;
	/**
	 * @var string Virtualaccount 振込依頼者氏名
	 */
	var $vaTradeClientName;
	/**
	 * @var string Virtualaccount 振込依頼者メールアドレス
	 */
	var $vaTradeClientMailaddress;
	/**
	 * @var string Virtualaccount 銀行コード
	 */
	var $vaBankCode;
	/**
	 * @var string Virtualaccount 銀行名
	 */
	var $vaBankName;
	/**
	 * @var string Virtualaccount 支店コード
	 */
	var $vaBranchCode;
	/**
	 * @var string Virtualaccount 支店名
	 */
	var $vaBranchName;
	/**
	 * @var string Virtualaccount 科目
	 */
	var $vaAccountType;
	/**
	 * @var string Virtualaccount 口座番号
	 */
	var $vaAccountNumber;
	/**
	 * @var string Virtualaccount 照会番号
	 */
	var $vaInInquiryNumber;
	/**
	 * @var string Virtualaccount 勘定日
	 */
	var $vaInSettlementDate;
	/**
	 * @var bigDecimal Virtualaccount 入金額
	 */
	var $vaInAmount;
	/**
	 * @var string Virtualaccount 振込依頼人コード
	 */
	var $vaInClientCode;
	/**
	 * @var string Virtualaccount 振込依頼人名
	 */
	var $vaInClientName;
	/**
	 * @var string Virtualaccount 摘要
	 */
	var $vaInSummary;
	/**
	 * @var string Virtualaccount 継続口座ID
	 */
	var $vaReserveID;
	/**
	 * @var string Recruit 注文番号
	 */
	var $rcOrderId;
	/**
	 * @var string Recruit 顧客IDハッシュ値
	 */
	var $rcCustomerId;
	/**
	 * @var string Recruit 注文時刻
	 */
	var $rcOrderTime;
	/**
	 * @var string Recruit 行使ポイント数
	 */
	var $rcUsePoint;
	/**
	 * @var string Recruit リクルート原資クーポン割引額
	 */
	var $rcUseCoupon;
	/**
	 * @var string Recruit 加盟店様原資クーポン割引額
	 */
	var $rcUseShopCoupon;
	/**
	 * @var string Recruit オーソリ期限延長実施日
	 */
	var $rcUpdateAuthDay;
	/**
	 * @var string RecruitContinuance 契約番号
	 */
	var $rcContractId;
	/**
	 * @var string RecruitContinuance 課金開始月
	 */
	var $rcStartChargeMonth;


	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params 出力パラメータ
	 */
	function SearchTradeMultiOutput($params = null) {
		$this->__construct($params);
	}

	/**
	 * コンストラクタ
	 *
	 * @param IgnoreCaseMap $params 出力パラメータ
	 */
	function __construct($params = null) {
		parent::__construct($params);

		// 引数が無い場合は戻る
        if (is_null($params)) {
            return;
        }

        // マップの展開
        $this->setOrderId($params->get('OrderID'));
        $this->setStatus($params->get('Status'));
        $this->setProcessDate($params->get('ProcessDate'));
       	$this->setJobCd($params->get('JobCd'));
       	$this->setAccessId($params->get('AccessID'));
       	$this->setAccessPass($params->get('AccessPass'));
       	$this->setItemCode($params->get('ItemCode'));
       	$this->setCurrency($params->get('Currency'));

       	$amount = $params->get('Amount');
       	$tax = $params->get('Tax');
       	$useBigDecimalAmount = $this->useBigDecimalAmount($params->get('PayType'));
		if ($useBigDecimalAmount === false) {
			$this->setAmount(is_numeric($amount) ? $amount : null);
            $this->setTax(is_numeric($tax) ? $tax : null);
		} else {
			$this->setAmountBigDecimal(is_numeric($amount) ? $amount : null);
        	$this->setTaxBigDecimal(is_numeric($tax) ? $tax : null);
		}

        $this->setSiteId($params->get('SiteID'));
        $this->setMemberId($params->get('MemberID'));
        $this->setCardNo($params->get('CardNo'));
        $this->setExpire($params->get('Expire'));
        $this->setMethod($params->get('Method'));
        $times = $params->get('PayTimes');
        if (!is_null($times) && 0 != strlen($times)) {
            // 数値の場合のみ値をセットする
            $this->setPayTimes(is_numeric($times) ? $times : null);
        }
        $this->setForward($params->get('Forward'));
        $this->setTranId($params->get('TranID'));

        $this->setApprovalNo($params->get('Approve'));
        $this->setClientField1($params->get('ClientField1'));
        $this->setClientField2($params->get('ClientField2'));
        $this->setClientField3($params->get('ClientField3'));

        $this->setPayType($params->get('PayType'));
		$this->setCvsCode($params->get('CvsCode'));
		$this->setCvsConfNo($params->get('CvsConfNo'));
		$this->setCvsReceiptNo($params->get('CvsReceiptNo'));
		$this->setEdyReceiptNo($params->get('EdyReceiptNo'));
		$this->setEdyOrderNo($params->get('EdyOrderNo'));
		$this->setSuicaReceiptNo($params->get('SuicaReceiptNo'));
		$this->setSuicaOrderNo($params->get('SuicaOrderNo'));
		$this->setCustId($params->get('CustId'));
		$this->setBkCode($params->get('BkCode'));
		$this->setConfNo($params->get('ConfNo'));
		$this->setPaymentTerm($params->get('PaymentTerm'));
		$this->setEncryptReceiptNo($params->get('EncryptReceiptNo'));

		$this->setWebMoneyManagementNo($params->get('WebMoneyManagementNo'));
		$this->setWebMoneySettleCode($params->get('WebMoneySettleCode'));

		$this->setAuPayInfoNo($params->get('AuPayInfoNo'));
		$this->setAuPayMethod($params->get('AuPayMethod'));
		$this->setAuCancelAmount($params->get('AuCancelAmount'));
		$this->setAuCancelTax($params->get('AuCancelTax'));

		$this->setDocomoSettlementCode($params->get('DocomoSettlementCode'));
		$this->setDocomoCancelAmount($params->get('DocomoCancelAmount'));
		$this->setDocomoCancelTax($params->get('DocomoCancelTax'));

		$this->setSbTrackingId($params->get('SbTrackingId'));
		$this->setSbCancelAmount($params->get('SbCancelAmount'));
		$this->setSbCancelTax($params->get('SbCancelTax'));


		$this->setFirstAmount($params->get('FirstAmount'));
		$this->setFirstTax($params->get('FirstTax'));
		$this->setAccountTimingKbn($params->get('AccountTimingKbn'));
		$this->setAccountTiming($params->get('AccountTiming'));
		$this->setFirstAccountDate($params->get('FirstAccountDate'));
		$this->setAuContinuanceErrCode($params->get('AuContinuanceErrCode'));
		$this->setAuContinuanceErrInfo($params->get('AuContinuanceErrInfo'));
		$this->setAuContinueAccountId($params->get('AuContinueAccountId'));

		$this->setJibunReceiptNo($params->get('JibunReceiptNo'));

		$this->setJcbPrecaSalesCode($params->get('JcbPrecaSalesCode'));
		$this->setNetCashPayType($params->get('NetCashPayType'));
		$this->setOrderDate($params->get('OrderDate'));
		$this->setCompletionDate($params->get('CompletionDate'));
		$this->setRakutenidCouponFee($params->get('RakutenidCouponFee'));
		$this->setLinepayProductName($params->get('LinepayProductName'));
		$this->setLinepayCancelAmount($params->get('LinepayCancelAmount'));
		$this->setLinepayCancelTax($params->get('LinepayCancelTax'));
		$this->setLinepayPayMethod($params->get('LinepayPayMethod'));
		$this->setCommodityName($params->get('CommodityName'));
		$this->setSbStartChargeMonth($params->get('SbStartChargeMonth'));
		$this->setVaRequestAmount($params->get('VaRequestAmount'));
		$this->setVaExpireDate($params->get('VaExpireDate'));
		$this->setVaTradeReason($params->get('VaTradeReason'));
		$this->setVaTradeClientName($params->get('VaTradeClientName'));
		$this->setVaTradeClientMailaddress($params->get('VaTradeClientMailaddress'));
		$this->setVaBankCode($params->get('VaBankCode'));
		$this->setVaBankName($params->get('VaBankName'));
		$this->setVaBranchCode($params->get('VaBranchCode'));
		$this->setVaBranchName($params->get('VaBranchName'));
		$this->setVaAccountType($params->get('VaAccountType'));
		$this->setVaAccountNumber($params->get('VaAccountNumber'));
		$this->setVaInInquiryNumber($params->get('VaInInquiryNumber'));
		$this->setVaInSettlementDate($params->get('VaInSettlementDate'));
		$this->setVaInAmount($params->get('VaInAmount'));
		$this->setVaInClientCode($params->get('VaInClientCode'));
		$this->setVaInClientName($params->get('VaInClientName'));
		$this->setVaInSummary($params->get('VaInSummary'));
		$this->setVaReserveID($params->get('VaReserveID'));
		$this->setRcOrderId($params->get('RcOrderId'));
		$this->setRcCustomerId($params->get('RcCustomerId'));
		$this->setRcOrderTime($params->get('RcOrderTime'));
		$this->setRcUsePoint($params->get('RcUsePoint'));
		$this->setRcUseCoupon($params->get('RcUseCoupon'));
		$this->setRcUseShopCoupon($params->get('RcUseShopCoupon'));
		$this->setRcUpdateAuthDay($params->get('RcUpdateAuthDay'));
		$this->setRcContractId($params->get('RcContractId'));
		$this->setRcStartChargeMonth($params->get('RcStartChargeMonth'));

	}

	function useBigDecimalAmount($payType) {
		$useBigDecimalAmount = false;
		$list = explode(",", USE_BIG_DECIMAL_AMOUNT_PAY_TYPES);
		for ($counter = 0; $counter < count($list); $counter++) {
			$useBigDecimalAmountPayType = $list[$counter];
			if ($useBigDecimalAmountPayType == $payType) {
				$useBigDecimalAmount = true;
				break;
			}
		}
		return $useBigDecimalAmount;
	}

	/**
	 * オーダーID取得
	 * @return string オーダーID
	 */
	function getOrderId() {
		return $this->orderId;
	}

	/**
	 * ステータス取得
	 * @return string ステータス
	 */
	function getStatus(){
		return $this->status;
	}

	/**
	 * 処理日時取得
	 * @return string 処理日時
	 */
	function getProcessDate(){
		return $this->processDate;
	}

	/**
	 * 処理区分取得
	 * @return string 処理区分
	 */
	function getJobCd(){
		return $this->jobCd;
	}

	/**
	 * 取引ID取得
	 * @return string 取引ID
	 */
	function getAccessId(){
		return $this->accessId;
	}

	/**
	 * 取引パスワード取得
	 * @return strig 取引パスワード
	 */
	function getAccessPass(){
		return $this->accessPass;
	}

	/**
	 * 商品コード取得
	 * @return string 商品コード
	 */
	function getItemCode(){
		return $this->itemCode;
	}
	/**
	 * 通貨コード取得
	 * @return string 通貨コード
	 */
	function getCurrency() {
	    return $this->currency;
	}
	/**
	 * 利用金額取得
	 * @return integer 利用金額
	 */
	function getAmount(){
		return $this->amount;
	}

	/**
	 * 税送料取得
	 * @return integer 税送料
	 */
	function getTax(){
		return $this->tax;
	}

	/**
	 * 利用金額取得
	 * @return BigDecimal 利用金額
	 */
	function getAmountBigDecimal(){
		return $this->amountBigDecimal;
	}

	/**
	 * 税送料取得
	 * @return BigDecimal 税送料
	 */
	function getTaxBigDecimal(){
		return $this->taxBigDecimal;
	}

	/**
	 * サイトID取得
	 * @return string サイトID
	 */
	function getSiteId(){
		return $this->siteId;
	}

	/**
	 * 会員ID
	 * @return string 会員ID
	 */
	function getMemberId(){
		return $this->memberId;
	}

	/**
	 * カード番号取得
	 * @return string カード番号(下4桁表示、以外マスク)
	 */
	function getCardNo(){
		return $this->cardNo;
	}

	/**
	 * カード有効期限取得
	 * @return string カード有効期限
	 */
	function getExpire(){
		return $this->expire;
	}

	/**
	 * 支払い方法取得
	 * @return string 支払方法コード
	 */
	function getMethod() {
		return $this->method;
	}

	/**
	 * 支払回数取得
	 * @return integer 支払回数
	 */
	function getPayTimes() {
		return $this->payTimes;
	}

	/**
	 * 仕向先コード取得
	 * @return string 仕向先コード
	 */
	function getForward(){
		return $this->forward;
	}

	/**
	 * トランザクションID取得
	 * @return　string トランザクションID
	 */
	function getTranId(){
		return $this->transactionId;
	}

	/**
	 * 承認番号取得
	 * @return string 承認番号
	 */
	function getApprovalNo(){
		return $this->approve;
	}

	/**
	 * 加盟店自由項目1取得
	 * @return string 加盟店自由項目1
	 */
	function getClientField1() {
		return $this->clientField1;
	}

	/**
	 * 加盟店自由項目2取得
	 * @return string 加盟店自由項目2
	 */
	function getClientField2() {
		return $this->clientField2;
	}

	/**
	 * 加盟店自由項目3取得
	 * @return string 加盟店自由項目3
	 */
	function getClientField3() {
		return $this->clientField3;
	}

	/**
	 * 決済手段取得
	 * @return  　string $string  決済手段
	 */
	function getPayType(){
		return $this->payType ;
	}


	/**
	 * 支払先コンビニ会社コード 取得
	 * @return  　string $string  支払先コンビニ会社コード
	 */
	function getCvsCode(){
		return $this->cvsCode ;
	}
	/**
	 * 支払先確認番号 取得
	 * @return  　string $string  支払先確認番号
	 */
	function getCvsConfNo(){
		return $this->cvsConfNo ;
	}

	/**
	 * 支払先コンビニ受付番号 取得
	 * @return  　string $string  支払先コンビニ会受付番号
	 */
	function getCvsReceiptNo(){
		return $this->cvsReceiptNo ;
	}
	/**
	 * Edy受付番号 取得
	 * @return  　string $string  Edy受付番号
	 */
	function getEdyReceiptNo(){
		return $this->edyReceiptNo ;
	}
	/**
	 * Edy注文番号 取得
	 * @return  　string $string  Edy注文番号
	 */
	function getEdyOrderNo(){
		return $this->edyOrderNo ;
	}
	/**
	 * Suica受付番号 取得
	 * @return  　string $string  Suica受付番号
	 */
	function getSuicaReceiptNo(){
		return $this->suicaReceiptNo ;
	}
	/**
	 * Suica注文番号 取得
	 * @return  　string $string  Suica注文番号
	 */
	function getSuicaOrderNo(){
		return $this->suicaOrderNo ;
	}
	/**
	 * Pay-easyお客様番号  取得
	 * @return  　string $string  Pay-easyお客様番号
	 */
	function getCustId(){
		return $this->custId ;
	}
	/**
	 * Pay-easy収納機関番号  取得
	 * @return  　string $string  Pay-easy収納機関番号
	 */
	function getBkCode(){
		return $this->bkCode ;
	}
	/**
	 * Pay-easy確認番号  取得
	 * @return  　string $string  Pay-easy確認番号
	 */
	function getConfNo(){
		return $this->confNo ;
	}
	/**
	 * Pay-easy暗号化決済番号  取得
	 * @return  　string $string  Pay-easy暗号化決済番号
	 */
	function getEncryptReceiptNo(){
		return $this->encryptReceiptNo ;
	}
	/**
	 * 支払期限日時  取得
	 * @return  　string $string  支払期限日時
	 */
	function getPaymentTerm(){
		return $this->paymentTerm ;
	}
	/**
	 * WebMoney管理番号 取得
	 * @return  　string $string  WebMoney管理番号
	 */
	function getWebmoneyManagementNo(){
		return $this->webmoneyManagementNo;
	}
	/**
	 * WebMoney決済コード 取得
	 * @return  　string $string  WebMoney決済コード
	 */
	function getWebmoneySettleCode(){
		return $this->webmoneySettleCode;
	}

	/**
	 * auかんたん決済決済情報番号 取得
	 * @return  　string $string  auかんたん決済情報番号
	 */
	function getAuPayInfoNo(){
		return $this->auPayInfoNo;
	}

	/**
	 * auかんたん決済支払方法 取得
	 * @return  　string $string  auかんたん決済支払方法
	 */
	function getAuPayMethod(){
		return $this->auPayMethod;
	}

	/**
	 * auかんたん決済キャンセル金額 取得
	 * @return  　string $string  auかんたん決済キャンセル金額
	 */
	function getAuCancelAmount(){
		return $this->auCancelAmount;
	}

	/**
	 * auかんたん決済キャンセル税送料 取得
	 * @return  　string $string  auかんたん決済キャンセル税送料
	 */
	function getAuCancelTax(){
		return $this->auCancelTax;
	}


	/**
	 * オーダーID設定
	 * @param string $orderId オーダーID
	 */
	function setOrderId($orderId) {
		$this->orderId = $orderId;
	}

	/**
	 * ステータス設定
	 * @param string $status ステータス
	 */
	function setStatus($status){
		$this->status = $status;
	}

	/**
	 * 処理日時設定
	 * @param string $processDate 処理日時
	 */
	function setProcessDate($processDate){
		$this->processDate = $processDate;
	}

	/**
	 * 処理区分設定
	 * @param string $jobCd 処理区分
	 */
	function setJobCd($jobCd){
		$this->jobCd = $jobCd;
	}

	/**
	 * 取引ID設定
	 * @param string $accessId 取引ID
	 */
	function setAccessId($accessId){
		$this->accessId = $accessId;
	}

	/**
	 * 取引パスワード設定
	 * @param string $accessPass 取引パスワード
	 */
	function setAccessPass($accessPass){
		$this->accessPass = $accessPass;
	}

	/**
	 * 商品コード設定
	 * @param string $itemCode 商品コード
	 */
	function setItemCode( $itemCode){
		$this->itemCode = $itemCode;
	}
	/**
	 * 通貨コード設定
	 * @param string
	 */
	function setCurrency( $currency ) {
	    $this->currency = $currency;
	}

	/**
	 * 利用金額設定
	 * @param string $amount 利用金額
	 */
	function setAmount($amount){
		$this->amount = $amount;
	}

	/**
	 * 税送料設定
	 * @param string $tax 税送料
	 */
	function setTax($tax){
		$this->tax = $tax;
	}

	/**
	 * 利用金額設定
	 * @param string $amountBigDecimal 利用金額
	 */
	function setAmountBigDecimal($amountBigDecimal){
		$this->amountBigDecimal = $amountBigDecimal;
	}

	/**
	 * 税送料設定
	 * @param string $taxBigDecimal 税送料
	 */
	function setTaxBigDecimal($taxBigDecimal){
		$this->taxBigDecimal = $taxBigDecimal;
	}

	/**
	 * サイトID設定
	 * @param string $siteId サイトID
	 */
	function setSiteId($siteId){
		$this->siteId = $siteId;
	}

	/**
	 * 会員ID設定
	 * @param string $memberId 会員ID
	 */
	function setMemberId($memberId){
		$this->memberId = $memberId;
	}

	/**
	 * カード番号設定
	 * @param string $cardNo カード番号(下4桁表示、以外マスク)
	 */
	function setCardNo($cardNo){
		$this->cardNo = $cardNo;
	}

	/**
	 * カード有効期限設定
	 * @param string $expire カード有効期限
	 */
	function setExpire( $expire ){
		$this->expire = $expire;
	}

	/**
	 * 支払い方法設定
	 * @param string $method 支払方法コード
	 */
	function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * 支払回数設定
	 * @param string $payTimes 支払回数
	 */
	function setPayTimes( $payTimes ) {
		$this->payTimes = $payTimes;
	}

	/**
	 * 仕向先コード設定
	 * @param string $forward 仕向け先コード
	 */
	function setForward($forward){
		$this->forward = $forward;
	}

	/**
	 * トランザクションID設定
	 * @param string $transactionId トランザクションID
	 */
	function setTranId($transactionId){
		$this->transactionId = $transactionId;
	}

	/**
	 * 承認番号設定
	 * @param string $approve 承認番号
	 */
	function setApprovalNo($approve){
		$this->approve = $approve;
	}

	/**
	 * 加盟店自由項目1設定
	 * @param string $clientField1 加盟店自由項目1
	 */
	function setClientField1($clientField1) {
		$this->clientField1 = $clientField1;
	}

	/**
	 * 加盟店自由項目2設定
	 * @param string $clientField2 加盟店自由項目2
	 */
	function setClientField2($clientField2) {
		$this->clientField2 = $clientField2;
	}

	/**
	 * 加盟店自由項目3設定
	 * @param string $clientField3 加盟店自由項目3
	 */
	function setClientField3($clientField3) {
		$this->clientField3 = $clientField3;
	}

	/**
	 * 決済手段設定
	 * @param  　string $string  決済手段
	 */
	function setPayType($string){
		$this->payType = $string;
	}

	/**
	 * 支払先コンビニ会社コード 設定
	 * @param  　string $string  支払先コンビニ会社コー
	 */
	function setCvsCode($string){
		$this->cvsCode = $string;
	}
	/**
	 * コンビニ確認番号 設定
	 * @param  　string $string  コンビニ確認番号
	 */
	function setCvsConfNo($string){
		$this->cvsConfNo = $string;
	}
	/**
	 * 支払先コンビニ受付番号 設定
	 * @param  　string $string  支払先コンビニ受付番号
	 */
	function setCvsReceiptNo($string){
		$this->cvsReceiptNo = $string;
	}
	/**
	 * Edy受付番号 設定
	 * @param  　string $string  Edy受付番号
	 */
	function setEdyReceiptNo($string){
		$this->edyReceiptNo = $string;
	}
	/**
	 * Edy注文番号   設定
	 * @param  　string $string  Edy注文番号
	 */
	function setEdyOrderNo($string){
		$this->edyOrderNo = $string;
	}
	/**
	 * Suica受付番号 設定
	 * @param  　string $string  Suica受付番号
	 */
	function setSuicaReceiptNo($string){
		$this->suicaReceiptNo = $string;
	}
	/**
	 * Suica注文番号 設定
	 * @param  　string $string  Suica注文番号
	 */
	function setSuicaOrderNo($string){
		$this->suicaOrderNo = $string;
	}
	/**
	 * Pay-easyお客様番号 設定
	 * @param  　string $string  Pay-easyお客様番号
	 */
	function setCustId($string){
		$this->custId = $string;
	}
	/**
	 * Pay-easy収納機関番号 設定
	 * @param  　string $string  Pay-easy収納機関番号
	 */
	function setBkCode($string){
		$this->bkCode = $string;
	}
	/**
	 * Pay-easy確認番号 設定
	 * @param  　string $string  Pay-easy確認番号
	 */
	function setConfNo($string){
		$this->confNo = $string;
	}
	/**
	 * 支払期限日時 設定
	 * @param  　string $string  支払期限日時
	 */
	function setPaymentTerm($string){
		$this->paymentTerm = $string;
	}
	/**
	 * 暗号化決済番号 設定
	 * @param  　string $string  暗号化決済番号
	 */
	function setEncryptReceiptNo($string){
		$this->encryptReceiptNo = $string;
	}
	/**
	 * WebMoney管理番号 設定
	 * @param  　string $string  WebMoney管理番号
	 */
	function setWebmoneyManagementNo($string){
		$this->webmoneyManagementNo = $string;
	}
	/**
	 * WebMoney決済コード 設定
	 * @param  　string $string  WebMoney決済コード
	 */
	function setWebmoneySettleCode($string){
		$this->webmoneySettleCode = $string;
	}

	/**
	 * auかんたん決済決済情報番号 設定
	 * @param  　string $string  auかんたん決済情報番号
	 */
	function setAuPayInfoNo($string){
		$this->auPayInfoNo = $string;
	}

	/**
	 * auかんたん決済支払方法 設定
	 * @param  　string $string  auかんたん決済支払方法
	 */
	function setAuPayMethod($string){
		$this->auPayMethod = $string;
	}

	/**
	 * auかんたん決済キャンセル金額 設定
	 * @param  　string $string  auかんたん決済キャンセル金額
	 */
	function setAuCancelAmount($string){
		$this->auCancelAmount = $string;
	}

	/**
	 * auかんたん決済キャンセル税送料 設定
	 * @param  　string $string  auかんたん決済キャンセル税送料
	 */
	function setAuCancelTax($string){
		$this->auCancelTax = $string;
	}

	/**
	 * ドコモ決済番号取得
	 * @return string ドコモ決済番号
	 */
	function getDocomoSettlementCode() {
		return $this->docomoSettlementCode;
	}

	/**
	 * ドコモキャンセル金額取得
	 * @return integer ドコモキャンセル金額
	 */
	function getDocomoCancelAmount() {
		return $this->docomoCancelAmount;
	}

	/**
	 * ドコモキャンセル税送料取得
	 * @return integer ドコモキャンセル税送料
	 */
	function getDocomoCancelTax() {
		return $this->docomoCancelTax;
	}

	/**
	 * ドコモ決済番号設定
	 *
	 * @param string $docomoSettlementCode
	 */
	function setDocomoSettlementCode($docomoSettlementCode) {
		$this->docomoSettlementCode = $docomoSettlementCode;
	}

	/**
	 * ドコモキャンセル金額設定
	 *
	 * @param integer $docomoCancelAmount
	 */
	function setDocomoCancelAmount($docomoCancelAmount) {
		$this->docomoCancelAmount = $docomoCancelAmount;
	}

	/**
	 * ドコモキャンセル税送料設定
	 *
	 * @param integer $docomoCancelTax
	 */
	function setDocomoCancelTax($docomoCancelTax) {
		$this->docomoCancelTax = $docomoCancelTax;
	}

	/**
	 * ソフトバンク処理トラッキングID取得
	 * @return string ソフトバンク処理トラッキングID
	 */
	function getSbTrackingId() {
		return $this->sbTrackingId;
	}

	/**
	 * ソフトバンクキャンセル金額取得
	 * @return integer ソフトバンクキャンセル金額
	 */
	function getSbCancelAmount() {
		return $this->sbCancelAmount;
	}
	/**
	 * ソフトバンクキャンセル税送料取得
	 * @return integer ソフトバンクキャンセル税送料
	 */
	function getSbCancelTax() {
		return $this->sbCancelTax;
	}

	/**
	 * ソフトバンク処理トラッキングID設定
	 *
	 * @param integer $sbTrackingId
	 */
	function setSbTrackingId($sbTrackingId) {
		$this->sbTrackingId = $sbTrackingId;
	}

	/**
	 * ソフトバンクキャンセル金額設定
	 *
	 * @param integer $sbCancelAmount
	 */
	function setSbCancelAmount($sbCancelAmount) {
		$this->sbCancelAmount = $sbCancelAmount;
	}

	/**
	 * ソフトバンクキャンセル税送料設定
	 *
	 * @param integer $sbCancelTax
	 */
	function setSbCancelTax($sbCancelTax) {
		$this->sbCancelTax = $sbCancelTax;
	}
	/**
	 * じぶん銀行受付番号取得
	 * @return じぶん銀行受付番号
	 */
	function getJibunReceiptNo(){
		return $this->jibunReceiptNo;
	}

	/**
	 * じぶん銀行受付番号設定
	 * @param  $jibunReceiptNo
	 */
	function setJibunReceiptNo($jibunReceiptNo){
		$this->jibunReceiptNo = $jibunReceiptNo;
	}




	/**
	 * au継続課金 初回課金利用金額取得
	 * @return integer 初回課金利用金額
	 */
	function getFirstAmount(){
		return $this->firstAmount;
	}

	/**
	 * au継続課金 初回課金利用金額設定
	 * @param integer $firstAmount
	 */
	function setFirstAmount($firstAmount){
		$this->firstAmount = $firstAmount;
	}

	/**
	 * au継続課金 初回課金税送料取得
	 * @return integer 初回課金税送料
	 */
	function getFirstTax(){
		return $this->firstTax;
	}

	/**
	 * au継続課金 初回課金税送料設定
	 * @param integer $firstTax
	 */
	function setFirstTax($firstTax){
		$this->firstTax = $firstTax;
	}

	/**
	 * au継続課金 課金タイミング区分取得
	 * @return string 課金タイミング区分
	 */
	function getAccountTimingKbn(){
		return $this->accountTimingKbn;
	}

	/**
	 * au継続課金 課金タイミング区分設定
	 * @param string $accountTimingKbn
	 */
	function setAccountTimingKbn($accountTimingKbn){
		$this->accountTimingKbn = $accountTimingKbn;
	}


	/**
	 * au継続課金 課金タイミング取得
	 * @return string 課金タイミング
	 */
	function getAccountTiming(){
		return $this->accountTiming;
	}

	/**
	 * au継続課金 課金タイミング設定
	 * @param string $accountTiming
	 */
	function setAccountTiming($accountTiming){
		$this->accountTiming= $accountTiming;
	}

	/**
	 * au継続課金 初回課金日取得
	 * @return string 初回課金日
	 */
	function getFirstAccountDate(){
		return $this->firstAccountDate;
	}

	/**
	 * au継続課金 初回課金日設定
	 * @param string $firstAccountDate
	 */
	function setFirstAccountDate($firstAccountDate){
		$this->firstAccountDate= $firstAccountDate;
	}


	/**
	 * au継続課金 エラーコード
	 * @return string エラーコード
	 */
	function getAuContinuanceErrCode(){
		return $this->auContinuaceErrCode;
	}

	/**
	 * au継続課金 エラーコード
	 * @param string $auContinuaceErrCode
	 */
	function setAuContinuanceErrCode($auContinuaceErrCode){
		$this->auContinuaceErrCode= $auContinuaceErrCode;
	}


	/**
	 * au継続課金 エラー詳細
	 * @return string エラー詳細
	 */
	function getAuContinuanceErrInfo(){
		return $this->auContinuaceErrInfo;
	}

	/**
	 * au継続課金 エラー詳細
	 * @param string $auContinuaceErrInfo
	 */
	function setAuContinuanceErrInfo($auContinuaceErrInfo){
		$this->auContinuaceErrInfo= $auContinuaceErrInfo;
	}



	/**
	 * au継続課金 au継続課金ID取得
	 * @return string au継続課金ID
	 */
	function getAuContinueAccountId(){
		return $this->auContinueAccountId;
	}

	/**
	 * au継続課金 au継続課金ID設定
	 * @param string $auContinueAccountId
	 */
	function setAuContinueAccountId($auContinueAccountId){
		$this->auContinueAccountId=$auContinueAccountId;
	}

	/**
	 * JcbPreca 伝票番号 取得
	 * @return string $伝票番号
	 */
	function getJcbPrecaSalesCode(){
		return $this->jcbPrecaSalesCode;
	}
	/**
	 * Netcash NET CASH決済方法 取得
	 * @return string $NET CASH決済方法
	 */
	function getNetCashPayType(){
		return $this->netCashPayType;
	}
	/**
	 * RakutenId 注文日 取得
	 * @return string $注文日
	 */
	function getOrderDate(){
		return $this->orderDate;
	}
	/**
	 * RakutenId 完了日 取得
	 * @return string $完了日
	 */
	function getCompletionDate(){
		return $this->completionDate;
	}
	/**
	 * RakutenId クーポン金額 取得
	 * @return integer $クーポン金額
	 */
	function getRakutenidCouponFee(){
		return $this->rakutenidCouponFee;
	}
	/**
	 * Linepay LINE Pay商品名 取得
	 * @return string $LINE Pay商品名
	 */
	function getLinepayProductName(){
		return $this->linepayProductName;
	}
	/**
	 * Linepay LINE Payキャンセル金額 取得
	 * @return bigDecimal $LINE Payキャンセル金額
	 */
	function getLinepayCancelAmount(){
		return $this->linepayCancelAmount;
	}
	/**
	 * Linepay LINE Payキャンセル税送料 取得
	 * @return bigDecimal $LINE Payキャンセル税送料
	 */
	function getLinepayCancelTax(){
		return $this->linepayCancelTax;
	}
	/**
	 * Linepay LINE Pay支払手段 取得
	 * @return string $LINE Pay支払手段
	 */
	function getLinepayPayMethod(){
		return $this->linepayPayMethod;
	}
	/**
	 * Unionpay 商品名 取得
	 * @return string $商品名
	 */
	function getCommodityName(){
		return $this->commodityName;
	}
	/**
	 * SbContinuance 課金開始月 取得
	 * @return string $課金開始月
	 */
	function getSbStartChargeMonth(){
		return $this->sbStartChargeMonth;
	}
	/**
	 * Virtualaccount 振込要求金額 取得
	 * @return bigDecimal $振込要求金額
	 */
	function getVaRequestAmount(){
		return $this->vaRequestAmount;
	}
	/**
	 * Virtualaccount 取引有効期限 取得
	 * @return string $取引有効期限
	 */
	function getVaExpireDate(){
		return $this->vaExpireDate;
	}
	/**
	 * Virtualaccount 取引事由 取得
	 * @return string $取引事由
	 */
	function getVaTradeReason(){
		return $this->vaTradeReason;
	}
	/**
	 * Virtualaccount 振込依頼者氏名 取得
	 * @return string $振込依頼者氏名
	 */
	function getVaTradeClientName(){
		return $this->vaTradeClientName;
	}
	/**
	 * Virtualaccount 振込依頼者メールアドレス 取得
	 * @return string $振込依頼者メールアドレス
	 */
	function getVaTradeClientMailaddress(){
		return $this->vaTradeClientMailaddress;
	}
	/**
	 * Virtualaccount 銀行コード 取得
	 * @return string $銀行コード
	 */
	function getVaBankCode(){
		return $this->vaBankCode;
	}
	/**
	 * Virtualaccount 銀行名 取得
	 * @return string $銀行名
	 */
	function getVaBankName(){
		return $this->vaBankName;
	}
	/**
	 * Virtualaccount 支店コード 取得
	 * @return string $支店コード
	 */
	function getVaBranchCode(){
		return $this->vaBranchCode;
	}
	/**
	 * Virtualaccount 支店名 取得
	 * @return string $支店名
	 */
	function getVaBranchName(){
		return $this->vaBranchName;
	}
	/**
	 * Virtualaccount 科目 取得
	 * @return string $科目
	 */
	function getVaAccountType(){
		return $this->vaAccountType;
	}
	/**
	 * Virtualaccount 口座番号 取得
	 * @return string $口座番号
	 */
	function getVaAccountNumber(){
		return $this->vaAccountNumber;
	}
	/**
	 * Virtualaccount 照会番号 取得
	 * @return string $照会番号
	 */
	function getVaInInquiryNumber(){
		return $this->vaInInquiryNumber;
	}
	/**
	 * Virtualaccount 勘定日 取得
	 * @return string $勘定日
	 */
	function getVaInSettlementDate(){
		return $this->vaInSettlementDate;
	}
	/**
	 * Virtualaccount 入金額 取得
	 * @return bigDecimal $入金額
	 */
	function getVaInAmount(){
		return $this->vaInAmount;
	}
	/**
	 * Virtualaccount 振込依頼人コード 取得
	 * @return string $振込依頼人コード
	 */
	function getVaInClientCode(){
		return $this->vaInClientCode;
	}
	/**
	 * Virtualaccount 振込依頼人名 取得
	 * @return string $振込依頼人名
	 */
	function getVaInClientName(){
		return $this->vaInClientName;
	}
	/**
	 * Virtualaccount 摘要 取得
	 * @return string $摘要
	 */
	function getVaInSummary(){
		return $this->vaInSummary;
	}
	/**
	 * Virtualaccount 継続口座ID 取得
	 * @return string $継続口座ID
	 */
	function getVaReserveID(){
		return $this->vaReserveID;
	}
	/**
	 * Recruit 注文番号 取得
	 * @return string $注文番号
	 */
	function getRcOrderId(){
		return $this->rcOrderId;
	}
	/**
	 * Recruit 顧客IDハッシュ値 取得
	 * @return string $顧客IDハッシュ値
	 */
	function getRcCustomerId(){
		return $this->rcCustomerId;
	}
	/**
	 * Recruit 注文時刻 取得
	 * @return string $注文時刻
	 */
	function getRcOrderTime(){
		return $this->rcOrderTime;
	}
	/**
	 * Recruit 行使ポイント数 取得
	 * @return string $行使ポイント数
	 */
	function getRcUsePoint(){
		return $this->rcUsePoint;
	}
	/**
	 * Recruit リクルート原資クーポン割引額 取得
	 * @return string $リクルート原資クーポン割引額
	 */
	function getRcUseCoupon(){
		return $this->rcUseCoupon;
	}
	/**
	 * Recruit 加盟店様原資クーポン割引額 取得
	 * @return string $加盟店様原資クーポン割引額
	 */
	function getRcUseShopCoupon(){
		return $this->rcUseShopCoupon;
	}
	/**
	 * Recruit オーソリ期限延長実施日 取得
	 * @return string $オーソリ期限延長実施日
	 */
	function getRcUpdateAuthDay(){
		return $this->rcUpdateAuthDay;
	}
	/**
	 * RecruitContinuance 契約番号 取得
	 * @return string $契約番号
	 */
	function getRcContractId(){
		return $this->rcContractId;
	}
	/**
	 * RecruitContinuance 課金開始月 取得
	 * @return string $課金開始月
	 */
	function getRcStartChargeMonth(){
		return $this->rcStartChargeMonth;
	}

	/**
	 * JcbPreca 伝票番号 設定
	 * @param string $jcbPrecaSalesCode
	 */
	function setJcbPrecaSalesCode($jcbPrecaSalesCode){
		$this->jcbPrecaSalesCode = $jcbPrecaSalesCode;
	}
	/**
	 * Netcash NET CASH決済方法 設定
	 * @param string $netCashPayType
	 */
	function setNetCashPayType($netCashPayType){
		$this->netCashPayType = $netCashPayType;
	}
	/**
	 * RakutenId 注文日 設定
	 * @param string $orderDate
	 */
	function setOrderDate($orderDate){
		$this->orderDate = $orderDate;
	}
	/**
	 * RakutenId 完了日 設定
	 * @param string $completionDate
	 */
	function setCompletionDate($completionDate){
		$this->completionDate = $completionDate;
	}
	/**
	 * RakutenId クーポン金額 設定
	 * @param integer $rakutenidCouponFee
	 */
	function setRakutenidCouponFee($rakutenidCouponFee){
		$this->rakutenidCouponFee = $rakutenidCouponFee;
	}
	/**
	 * Linepay LINE Pay商品名 設定
	 * @param string $linepayProductName
	 */
	function setLinepayProductName($linepayProductName){
		$this->linepayProductName = $linepayProductName;
	}
	/**
	 * Linepay LINE Payキャンセル金額 設定
	 * @param bigDecimal $linepayCancelAmount
	 */
	function setLinepayCancelAmount($linepayCancelAmount){
		$this->linepayCancelAmount = $linepayCancelAmount;
	}
	/**
	 * Linepay LINE Payキャンセル税送料 設定
	 * @param bigDecimal $linepayCancelTax
	 */
	function setLinepayCancelTax($linepayCancelTax){
		$this->linepayCancelTax = $linepayCancelTax;
	}
	/**
	 * Linepay LINE Pay支払手段 設定
	 * @param string $linepayPayMethod
	 */
	function setLinepayPayMethod($linepayPayMethod){
		$this->linepayPayMethod = $linepayPayMethod;
	}
	/**
	 * Unionpay 商品名 設定
	 * @param string $commodityName
	 */
	function setCommodityName($commodityName){
		$this->commodityName = $commodityName;
	}
	/**
	 * SbContinuance 課金開始月 設定
	 * @param string $sbStartChargeMonth
	 */
	function setSbStartChargeMonth($sbStartChargeMonth){
		$this->sbStartChargeMonth = $sbStartChargeMonth;
	}
	/**
	 * Virtualaccount 振込要求金額 設定
	 * @param bigDecimal $vaRequestAmount
	 */
	function setVaRequestAmount($vaRequestAmount){
		$this->vaRequestAmount = $vaRequestAmount;
	}
	/**
	 * Virtualaccount 取引有効期限 設定
	 * @param string $vaExpireDate
	 */
	function setVaExpireDate($vaExpireDate){
		$this->vaExpireDate = $vaExpireDate;
	}
	/**
	 * Virtualaccount 取引事由 設定
	 * @param string $vaTradeReason
	 */
	function setVaTradeReason($vaTradeReason){
		$this->vaTradeReason = $vaTradeReason;
	}
	/**
	 * Virtualaccount 振込依頼者氏名 設定
	 * @param string $vaTradeClientName
	 */
	function setVaTradeClientName($vaTradeClientName){
		$this->vaTradeClientName = $vaTradeClientName;
	}
	/**
	 * Virtualaccount 振込依頼者メールアドレス 設定
	 * @param string $vaTradeClientMailaddress
	 */
	function setVaTradeClientMailaddress($vaTradeClientMailaddress){
		$this->vaTradeClientMailaddress = $vaTradeClientMailaddress;
	}
	/**
	 * Virtualaccount 銀行コード 設定
	 * @param string $vaBankCode
	 */
	function setVaBankCode($vaBankCode){
		$this->vaBankCode = $vaBankCode;
	}
	/**
	 * Virtualaccount 銀行名 設定
	 * @param string $vaBankName
	 */
	function setVaBankName($vaBankName){
		$this->vaBankName = $vaBankName;
	}
	/**
	 * Virtualaccount 支店コード 設定
	 * @param string $vaBranchCode
	 */
	function setVaBranchCode($vaBranchCode){
		$this->vaBranchCode = $vaBranchCode;
	}
	/**
	 * Virtualaccount 支店名 設定
	 * @param string $vaBranchName
	 */
	function setVaBranchName($vaBranchName){
		$this->vaBranchName = $vaBranchName;
	}
	/**
	 * Virtualaccount 科目 設定
	 * @param string $vaAccountType
	 */
	function setVaAccountType($vaAccountType){
		$this->vaAccountType = $vaAccountType;
	}
	/**
	 * Virtualaccount 口座番号 設定
	 * @param string $vaAccountNumber
	 */
	function setVaAccountNumber($vaAccountNumber){
		$this->vaAccountNumber = $vaAccountNumber;
	}
	/**
	 * Virtualaccount 照会番号 設定
	 * @param string $vaInInquiryNumber
	 */
	function setVaInInquiryNumber($vaInInquiryNumber){
		$this->vaInInquiryNumber = $vaInInquiryNumber;
	}
	/**
	 * Virtualaccount 勘定日 設定
	 * @param string $vaInSettlementDate
	 */
	function setVaInSettlementDate($vaInSettlementDate){
		$this->vaInSettlementDate = $vaInSettlementDate;
	}
	/**
	 * Virtualaccount 入金額 設定
	 * @param bigDecimal $vaInAmount
	 */
	function setVaInAmount($vaInAmount){
		$this->vaInAmount = $vaInAmount;
	}
	/**
	 * Virtualaccount 振込依頼人コード 設定
	 * @param string $vaInClientCode
	 */
	function setVaInClientCode($vaInClientCode){
		$this->vaInClientCode = $vaInClientCode;
	}
	/**
	 * Virtualaccount 振込依頼人名 設定
	 * @param string $vaInClientName
	 */
	function setVaInClientName($vaInClientName){
		$this->vaInClientName = $vaInClientName;
	}
	/**
	 * Virtualaccount 摘要 設定
	 * @param string $vaInSummary
	 */
	function setVaInSummary($vaInSummary){
		$this->vaInSummary = $vaInSummary;
	}
	/**
	 * Virtualaccount 継続口座ID 設定
	 * @param string $vaReserveID
	 */
	function setVaReserveID($vaReserveID){
		$this->vaReserveID = $vaReserveID;
	}
	/**
	 * Recruit 注文番号 設定
	 * @param string $rcOrderId
	 */
	function setRcOrderId($rcOrderId){
		$this->rcOrderId = $rcOrderId;
	}
	/**
	 * Recruit 顧客IDハッシュ値 設定
	 * @param string $rcCustomerId
	 */
	function setRcCustomerId($rcCustomerId){
		$this->rcCustomerId = $rcCustomerId;
	}
	/**
	 * Recruit 注文時刻 設定
	 * @param string $rcOrderTime
	 */
	function setRcOrderTime($rcOrderTime){
		$this->rcOrderTime = $rcOrderTime;
	}
	/**
	 * Recruit 行使ポイント数 設定
	 * @param string $rcUsePoint
	 */
	function setRcUsePoint($rcUsePoint){
		$this->rcUsePoint = $rcUsePoint;
	}
	/**
	 * Recruit リクルート原資クーポン割引額 設定
	 * @param string $rcUseCoupon
	 */
	function setRcUseCoupon($rcUseCoupon){
		$this->rcUseCoupon = $rcUseCoupon;
	}
	/**
	 * Recruit 加盟店様原資クーポン割引額 設定
	 * @param string $rcUseShopCoupon
	 */
	function setRcUseShopCoupon($rcUseShopCoupon){
		$this->rcUseShopCoupon = $rcUseShopCoupon;
	}
	/**
	 * Recruit オーソリ期限延長実施日 設定
	 * @param string $rcUpdateAuthDay
	 */
	function setRcUpdateAuthDay($rcUpdateAuthDay){
		$this->rcUpdateAuthDay = $rcUpdateAuthDay;
	}
	/**
	 * RecruitContinuance 契約番号 設定
	 * @param string $rcContractId
	 */
	function setRcContractId($rcContractId){
		$this->rcContractId = $rcContractId;
	}
	/**
	 * RecruitContinuance 課金開始月 設定
	 * @param string $rcStartChargeMonth
	 */
	function setRcStartChargeMonth($rcStartChargeMonth){
		$this->rcStartChargeMonth = $rcStartChargeMonth;
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
	    $str .= 'Status=' . $this->getStatus();
        $str .= '&';
	    $str .= 'ProcessDate=' . $this->getProcessDate();
        $str .= '&';
	    $str .= 'JobCd=' . $this->getJobCd();
        $str .= '&';
	    $str .= 'AccessID=' . $this->getAccessId();
        $str .= '&';
	    $str .= 'AccessPass=' . $this->getAccessPass();
        $str .= '&';
	    $str .= 'ItemCode=' . $this->getItemCode();
        $str .= '&';
        $str .= 'Amount=' . $this->getAmount();
        $str .= '&';
	    $str .= 'Tax=' . $this->getTax();
        $str .= '&';
	    $str .= 'AmountBigDecimal=' . $this->getAmountBigDecimal();
        $str .= '&';
	    $str .= 'TaxBigDecimal=' . $this->getTaxBigDecimal();
        $str .= '&';
	    $str .= 'SiteID=' . $this->getSiteId();
        $str .= '&';
	    $str .= 'MemberID=' . $this->getMemberId();
        $str .= '&';
	    $str .= 'CardNo=' . $this->getCardNo();
        $str .= '&';
	    $str .= 'Expire=' . $this->getExpire();
        $str .= '&';
        $str .= 'Method=' . $this->getMethod();
        $str .= '&';
        $str .= 'PayTimes=' . $this->getPayTimes();
        $str .= '&';
        $str .= 'Forward=' . $this->getForward();
        $str .= '&';
        $str .= 'TranID=' . $this->getTranId();
        $str .= '&';
        $str .= 'Approve=' . $this->getApprovalNo();
        $str .= '&';
        $str .= 'ClientField1=' . $this->getClientField1();
        $str .= '&';
        $str .= 'ClientField2=' . $this->getClientField2();
        $str .= '&';
        $str .= 'ClientField3=' . $this->getClientField3();
		$str .= '&';
        $str .= 'PayType=' . $this->getPayType();
		$str .= '&';
        $str .= 'CvsCode=' . $this->getCvsCode();
		$str .= '&';
        $str .= 'CvsConfNo=' . $this->getCvsConfNo();
		$str .= '&';
        $str .= 'CvsReceiptNo=' . $this->getCvsReceiptNo();
		$str .= '&';
        $str .= 'EdyReceiptNo=' . $this->getEdyReceiptNo();
		$str .= '&';
        $str .= 'EdyOrderNo=' . $this->getEdyOrderNo();
		$str .= '&';
        $str .= 'SuicaReceiptNo=' . $this->getSuicaReceiptNo();
		$str .= '&';
        $str .= 'SuicaOrderNo=' . $this->getSuicaOrderNo();
		$str .= '&';
        $str .= 'CustId=' . $this->getCustId();
		$str .= '&';
        $str .= 'BkCode=' . $this->getBkCode();
		$str .= '&';
        $str .= 'ConfNo=' . $this->getConfNo();
		$str .= '&';
        $str .= 'PaymentTerm=' . $this->getPaymentTerm();
        $str .= '&';
        $str .= 'EncryptReceiptNo=' . $this->getEncryptReceiptNo();
        $str .= '&';
        $str .= 'WebMoneyManagementNo=' . $this->getWebMoneyManagementNo();
        $str .= '&';
        $str .= 'WebMoneySettleCode=' . $this->getWebMoneySettleCode();
        $str .= '&';
		$str .= 'AuPayInfoNo=' . $this->getAuPayInfoNo();
        $str .= '&';
		$str .= 'AuPayMethod=' . $this->getAuPayMethod();
        $str .= '&';
		$str .= 'AuCancelAmount=' . $this->getAuCancelAmount();
        $str .= '&';
		$str .= 'AuCancelTax=' . $this->getAuCancelTax();
		$str .= '&';
		$str .= 'DocomoSettlementCode=' . $this->encodeStr($this->getDocomoSettlementCode());
		$str .= '&';
		$str .= 'DocomoCancelAmount=' . $this->encodeStr($this->getDocomoCancelAmount());
		$str .= '&';
		$str .= 'DocomoCancelTax=' . $this->encodeStr($this->getDocomoCancelTax());
		$str .= '&';
		$str .= 'SbTrackingId=' . $this->encodeStr($this->getSbTrackingId());
		$str .= '&';
		$str .= 'SbCancelAmount=' . $this->encodeStr($this->getSbCancelAmount());
		$str .= '&';
		$str .= 'SbCancelTax=' . $this->encodeStr($this->getSbCancelTax());
		$str .= '&';
		$str .= 'JibunReceiptNo=' . $this->getJibunReceiptNo();
		$str .= '&';
		$str .= 'FirstAmount=' . $this->getFirstAmount();
		$str .= '&';
		$str .= 'FirstTax=' . $this->getFirstTax();
		$str .= '&';
		$str .= 'AccountTimingKbn=' . $this->getAccountTimingKbn();
		$str .= '&';
		$str .= 'AccountTiming=' . $this->getAccountTiming();
		$str .= '&';
		$str .= 'FirstAccountDate=' . $this->getFirstAccountDate();
		$str .= '&';
		$str .= 'AuContinuaceErrCode=' . $this->getAuContinuanceErrCode();
		$str .= '&';
		$str .= 'AuContinuaceErrInfo=' . $this->getAuContinuanceErrInfo();
		$str .= '&';
		$str .= 'AuContinueAccountId=' . $this->getAuContinueAccountId();

		$str .= '&';
		$str .= 'JcbPrecaSalesCode=' . $this->getJcbPrecaSalesCode();
		$str .= '&';
		$str .= 'NetCashPayType=' . $this->getNetCashPayType();
		$str .= '&';
		$str .= 'OrderDate=' . $this->getOrderDate();
		$str .= '&';
		$str .= 'CompletionDate=' . $this->getCompletionDate();
		$str .= '&';
		$str .= 'RakutenidCouponFee=' . $this->getRakutenidCouponFee();
		$str .= '&';
		$str .= 'LinepayProductName=' . $this->getLinepayProductName();
		$str .= '&';
		$str .= 'LinepayCancelAmount=' . $this->getLinepayCancelAmount();
		$str .= '&';
		$str .= 'LinepayCancelTax=' . $this->getLinepayCancelTax();
		$str .= '&';
		$str .= 'LinepayPayMethod=' . $this->getLinepayPayMethod();
		$str .= '&';
		$str .= 'CommodityName=' . $this->getCommodityName();
		$str .= '&';
		$str .= 'SbStartChargeMonth=' . $this->getSbStartChargeMonth();
		$str .= '&';
		$str .= 'VaRequestAmount=' . $this->getVaRequestAmount();
		$str .= '&';
		$str .= 'VaExpireDate=' . $this->getVaExpireDate();
		$str .= '&';
		$str .= 'VaTradeReason=' . $this->getVaTradeReason();
		$str .= '&';
		$str .= 'VaTradeClientName=' . $this->getVaTradeClientName();
		$str .= '&';
		$str .= 'VaTradeClientMailaddress=' . $this->getVaTradeClientMailaddress();
		$str .= '&';
		$str .= 'VaBankCode=' . $this->getVaBankCode();
		$str .= '&';
		$str .= 'VaBankName=' . $this->getVaBankName();
		$str .= '&';
		$str .= 'VaBranchCode=' . $this->getVaBranchCode();
		$str .= '&';
		$str .= 'VaBranchName=' . $this->getVaBranchName();
		$str .= '&';
		$str .= 'VaAccountType=' . $this->getVaAccountType();
		$str .= '&';
		$str .= 'VaAccountNumber=' . $this->getVaAccountNumber();
		$str .= '&';
		$str .= 'VaInInquiryNumber=' . $this->getVaInInquiryNumber();
		$str .= '&';
		$str .= 'VaInSettlementDate=' . $this->getVaInSettlementDate();
		$str .= '&';
		$str .= 'VaInAmount=' . $this->getVaInAmount();
		$str .= '&';
		$str .= 'VaInClientCode=' . $this->getVaInClientCode();
		$str .= '&';
		$str .= 'VaInClientName=' . $this->getVaInClientName();
		$str .= '&';
		$str .= 'VaInSummary=' . $this->getVaInSummary();
		$str .= '&';
		$str .= 'VaReserveID=' . $this->getVaReserveID();
		$str .= '&';
		$str .= 'RcOrderId=' . $this->getRcOrderId();
		$str .= '&';
		$str .= 'RcCustomerId=' . $this->getRcCustomerId();
		$str .= '&';
		$str .= 'RcOrderTime=' . $this->getRcOrderTime();
		$str .= '&';
		$str .= 'RcUsePoint=' . $this->getRcUsePoint();
		$str .= '&';
		$str .= 'RcUseCoupon=' . $this->getRcUseCoupon();
		$str .= '&';
		$str .= 'RcUseShopCoupon=' . $this->getRcUseShopCoupon();
		$str .= '&';
		$str .= 'RcUpdateAuthDay=' . $this->getRcUpdateAuthDay();
		$str .= '&';
		$str .= 'RcContractId=' . $this->getRcContractId();
		$str .= '&';
		$str .= 'RcStartChargeMonth=' . $this->getRcStartChargeMonth();


        if ($this->isErrorOccurred()) {
            // エラー文字列を連結して返す
            $errString = parent::toString();
            $str .= '&' . $errString;
        }

        return $str;
	}

}
?>
