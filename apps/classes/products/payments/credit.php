<?php
include_once(dirname(__FILE__) . '/BasePayment.php');
include_once('com/gmo_pg/client/input/EntryTranInput.php');
include_once('com/gmo_pg/client/tran/EntryTran.php');
include_once('com/gmo_pg/client/input/ExecTranInput.php');
include_once('com/gmo_pg/client/tran/ExecTran.php');
include_once('com/gmo_pg/client/input/SearchTradeMultiInput.php');
include_once('com/gmo_pg/client/tran/SearchTradeMulti.php');
include_once('com/gmo_pg/client/tran/SaveMember.php');
include_once('com/gmo_pg/client/input/SaveMemberInput.php');
include_once('com/gmo_pg/client/tran/SaveCard.php');
include_once('com/gmo_pg/client/input/SaveCardInput.php');
include_once('com/gmo_pg/client/tran/DeleteMember.php');
include_once('com/gmo_pg/client/input/DeleteMemberInput.php');
include_once('com/gmo_pg/client/tran/SearchCard.php');
include_once('com/gmo_pg/client/input/SearchCardInput.php');


class Credit extends BasePayment
{
	/**
	 * 支払いタイプ : クレジット
	 *
	 */
	const payType = 0;

	/**
	 * 通貨コード
	 */
	public $currencyCode = 'USD';

	/**
	 * 決済方法
	 * @var string  CAPTURE:即売り上げ  AUTH:仮売上
	 */
	public $jobCD = 'CAPTURE';

	/**
	 * 3Dセキュア使用フラグ 0：行なわない(デフォルト) 1：行なう
	 * @var int
	 */
	public $TdFlag = 0;

	/**
	 * クレジットカードデェフォルトフラグ
	 * 最後に登録したものをデェフォルトにする。
	 */
	public $cardfaultFlag = 1;

	/**
	 * result
	 * @var null
	 */
	public $payResult = null;

	/**
	 * logger
	 * @var Logger|null
	 */
	private $logger = null;


	/**
	 * Credit constructor.
	 */
	public function __construct()
	{
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	//取引登録  P273多通過クレジットカード決済
	/**
	 * 取引の登録
	 * @param string $orderId
	 * @param int $amount
	 * @param int $tax
	 * @param string $shopName
	 * @throws Exception
	 */
	public function add($orderId = '', $amount = 0, $tax = 0, $tdTenantName = '')
	{
		$this->checkInit();
		$input = new EntryTranInput();
		$input->setShopID($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setOrderID($orderId);
		$input->setJobCd($this->jobCD);
		$input->setTax($tax);
		$input->setAmount($amount);
		$input->setTdFlag($this->TdFlag);
		$input->setTdTenantName(
			base64_decode(
				mb_convert_encoding($tdTenantName, 'EUC-JP')
			)
		);
		$entry = new EntryTran();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->errorList = $result->getErrList();
			$this->logger->error($result->getErrList());
			throw new Exception($this->getErrorMessage());
		}
		return [
			'AccessID' => $result->getAccessId(),
			'AccessPass' => $result->getAccessPass(),
			'OrderID' => $orderId
		];
	}

	/**
	 * 決済
	 * @param array $access
	 * @param string $cardNo
	 * @param string $cardExpire
	 * @param string $securityCode
	 * @param int $method
	 * @param int $paytime
	 * @param array $cientField
	 * @return array
	 * @throws Exception
	 */
	public function pay(
		$access,
		$cardNo,
		$cardExpire,
		$securityCode,
		$method = 1,
		$paytime = 1
	)
	{
		$input = new ExecTranInput();
		$input->setAccessId($access['AccessID']);
		$input->setAccessPass($access['AccessPass']);
		$input->setOrderId($access['OrderID']);
		$input->setMethod($method);
		$input->setPayTimes($paytime);
		$input->setCardNo($cardNo);
		$input->setExpire($cardExpire);
		$input->setSecurityCode($securityCode);
		$entry = new ExecTran();
		$result = $entry->exec($input);
		if (isset($result->errList) && $result->errList) {
			$this->errorList = $result->errList;
			throw new Exception($this->getErrorMessage());
		}
		$this->payResult = [
			'orderId' => $result->OrderID,  //注文コード gmo_payment_order_id
			'ReceiptNo' => null,            //受付番号 creditの場合はない。
			'TranDate' => $result->TranDate  //決済日
		];
		return true;
	}

	/**
	 * 登録済みカード情報から決済する
	 * @param array $access
	 * @param string $memberId
	 * @param int $cardSec
	 */
	public function payBySaveCard($access = [], $memberId = '', $cardSec = 0)
	{
		$input = new ExecTranInput();
		$input->setAccessId($access['AccessID']);
		$input->setAccessPass($access['AccessPass']);
		$input->setOrderId($access['OrderID']);
		$input->setSiteId($this->siteId);
		$input->setSitePass($this->sitePass);
		$input->setMemberId($memberId);
		$input->setCardSeq($cardSec);
		$input->setMethod(1);
		$input->setPayTimes(1);
		$entry = new ExecTran();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->errorList = $result->getErrList();
			$this->logger->error($result->errList);
			throw new Exception('保存に失敗しました。');
		}
		return true;
	}

	/**
	 * 決済情報の取得
	 * @param string $orderId
	 * @return mixed|string
	 */
	public function status($orderId)
	{
		$input = new SearchTradeMultiInput();
		$input->setShopId($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setPayType(self::payType);
		$input->setOrderId($orderId);
		$entry = new SearchTradeMulti();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->errorList = $result->getErrList();
			throw new Exception($this->getErrorMessage());
		}
		$status = $result->getStatus();

		if ($status && isset($this->statusList[$status])) {
			return $this->statusList[$status];
		}
		return $status;
	}

	/**
	 * 会員登録
	 * @param string $memberId
	 * @param string $memberName
	 * @return string memberId
	 * @throws Exception
	 */
	public function registMenber($memberId = '', $memberName = '')
	{
		//TODO:siteIDの設定を確認する
		$input = new SaveMemberInput();
		$input->setSiteId($this->siteId);
		$input->setSitePass($this->sitePass);
		$input->setMemberId($memberId);
		$input->setMemberName($memberName);
		$entry = new  SaveMember();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->errorList = $result->getErrList();
			throw new Exception($this->getErrorMessage());
		}
		return $result->getMemberId();
	}

	/**
	 * クレジットカードの登録
	 * @param string $memberId
	 * @param string $cardNo
	 * @param string $Expire
	 * @param string $holderName
	 */
	public function saveCard($memberId = '', $cardNo = '', $Expire = '', $holderName = '')
	{
		$input = new SaveCardInput();
		$input->setSiteId($this->siteId);
		$input->setSitePass($this->sitePass);
		$input->setMemberId($memberId);
		$input->setCardNo($cardNo);
		$input->setExpire($Expire);
		$input->setHolderName($holderName);
		$input->setDefaultFlag($this->cardfaultFlag);
		$entry = new SaveCard();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			//エラー処理
			$this->errorList = $result->getErrList();
			throw new Exception('カードの登録失敗');

		}
		return true;
	}

	/**
	 * 会員情報の削除
	 * @param $memberId
	 * @return bool
	 * @throws Exception
	 */
	public function removeMember($memberId)
	{
		$input = new DeleteMemberInput();
		$input->setSiteId($this->siteId);
		$input->setSitePass($this->sitePass);
		$input->setMemberId($memberId);
		$entry = new DeleteMember();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			//エラー処理
			$this->errorList = $result->getErrList();
			throw new Exception('会員情報削除失敗');
		}
		return true;
	}

	/**
	 * カード情報の取得
	 * @param string $memberId
	 * @return array|null
	 * @throws Exception
	 */
	public function getCard($memberId = '')
	{
		$input = new SearchCardInput();
		$input->setSiteId($this->siteId);
		$input->setSitePass($this->sitePass);
		$input->setMemberId($memberId);
		$entry = new SearchCard();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			//エラー処理
			$this->errorList = $result->getErrList();
			throw new Exception($this->getErrorMessage());
		}
		$list = $result->cardList;
		krsort($list);
		return $list; //最後に登録された順
	}

	/**
	 * エラーメッセージ取得
	 * @return mixed|null|string
	 */
	public function getErrorMessage()
	{
		$error = [];
		$other = [];
		if(isset($this->errorList))
		{
			foreach ($this->errorList as $key => $item)
			{
				if(isset($item->errInfo) && isset($this->errorMessage[$item->errInfo]))
				{
					$error[] = $this->errorMessage[$item->errInfo];
				}
				elseif (isset($item->errInfo))
				{
					$other[] = $item->errInfo;
				}
			}
		}

		if($error)
		{
			return join("\n", $error);
		}
		elseif ($other)
		{
			return $this->errorMessage['OTHER'];
		}
		return null;
	}
}
