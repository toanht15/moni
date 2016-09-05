<?php
include_once(dirname(__FILE__) . '/BasePayment.php');
include_once('com/gmo_pg/client/tran/EntryTranCvs.php');
include_once('com/gmo_pg/client/tran/EntryTran.php');
include_once('com/gmo_pg/client/input/EntryTranCvsInput.php');
include_once('com/gmo_pg/client/input/EntryTranInput.php');
include_once('com/gmo_pg/client/input/EntryExecTranCvsInput.php');
include_once('com/gmo_pg/client/tran/ExecTranCvs.php');
include_once('com/gmo_pg/client/tran/SearchTradeMulti.php');
include_once('com/gmo_pg/client/input/SearchTradeMultiInput.php');

class convenienceStore extends BasePayment
{
	/**
	 * コンビニ払い支払いタイプ
	 */
	const payType = 3;
	/**
	 * @var
	 */
	public $shopId = null;
	/**
	 * @var
	 */
	public $shopPass = null;
	/**
	 * @var
	 */
	public $orderId;

	/**
	 * 支払い有効期限
	 * @var int
	 */
	public $limitDay = 6;

	/**
	 * 支払い結果
	 * @var null
	 */
	public $payResult = null;

	/**
	 * logger
	 * @var Logger|null
	 */
	private $logger = null;

	/**
	 * convenienceStore constructor.
	 */
	public function __construct()
	{
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	/**
	 * 支払い有効期限の設定
	 * @param int $day
	 */
	public function setLimitDay($day = 0)
	{
		$this->limitDay = $day;
	}

	/**
	 * 取引の登録
	 * @param string $orderId
	 * @param int $amount
	 * @param int $tax
	 * @return array
	 * @throws Exception
	 */
	public function add($orderId = '', $amount = 0, $tax = 0)
	{
		$this->checkInit();
		$input = new EntryTranCvsInput();
		$input->setShopId($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setOrderId($orderId);
		$input->setAmount($amount);
		$input->setTax($tax);
		$input->PaymentTermDay = $this->limitDay;
		$entry = new EntryTranCvs();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->logger->error($result->getErrList());
			$this->errorList = $result->getErrList();
			throw new Exception($this->getErrorMessage());
		}
		return [
			'OrderID' => $orderId,
			'AccessID' => $result->getAccessId(),
			'AccessPass' => $result->getAccessPass()
		];
	}

	public function pay(
		$access = [],
		$convenienceCode,
		$customerName,
		$customerNameKana,
		$telNo,
		$ReceiptsDisp11,
		$ReceiptsDisp12,
		$ReceiptsDisp13
	) {
		$input = new ExecTranCvsInput();
		$input->setAccessId($access['AccessID']);
		$input->setAccessPass($access['AccessPass']);
		$input->setOrderId($access['OrderID']);
		$input->setConvenience($convenienceCode);
		$input->setCustomerName(mb_convert_encoding($customerName, 'SJIS-WIN', 'UTF8'));
		$input->setCustomerKana(mb_convert_encoding($customerNameKana, 'SJIS-WIN', 'UTF8'));
		$input->setReceiptsDisp11(mb_convert_encoding($ReceiptsDisp11, 'SJIS-WIN', 'UTF8')); //お問い合わせ先
		$input->setReceiptsDisp12($ReceiptsDisp12); //お問い合わせ電話番号
		$input->setReceiptsDisp13($ReceiptsDisp13); //お問い合わせ時間
		$input->setTelNo($telNo);
		$entry = new ExecTranCvs();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->logger->error($result->getErrList());
			$this->errorList = $result->getErrList();
			throw new Exception($this->getErrorMessage());
		}
		$this->payResult = [
			'OrderID' => $result->OrderID, //注文コード gmo_payment_order_id
			'confNo' => $result->confNo,
			'receiptNo' => $result->receiptNo,
			'paymentTerm' => $result->paymentTerm,
			'tranDate' => $result->tranDate, //決済日時
			'receiptUrl' => $result->receiptUrl,
			'checkString'=>$result->checkString,
			'clientField1' => $result->clientField1,
			'clientField2' => $result->clientField2,
			'clientField3' => $result->clientField3
		];
		return true;
	}

	/**
	 * 決済情報の取得
	 * @param string $orderId
	 */
	public function status($orderId = '')
	{
		$input = new SearchTradeMultiInput();
		$input->setShopId($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setPayType(self::payType);
		$input->setOrderId($orderId);
		$entry = new SearchTradeMulti();
		$result = $entry->exec($input);
		$status = $result->getStatus();
		if ($status && isset($this->statusList[$status])) {
			return $this->statusList[$status];
		}
		return $status;
	}
}
