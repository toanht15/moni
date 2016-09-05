<?php
include_once(dirname(__FILE__). '/BasePayment.php');
include_once('com/gmo_pg/client/input/EntryTranRakutenIdInput.php');
include_once('com/gmo_pg/client/input/RakutenIdSalesInput.php');
include_once('com/gmo_pg/client/tran/EntryTranRakutenId.php');
include_once('com/gmo_pg/client/tran/RakutenIdSales.php');
include_once('com/gmo_pg/client/input/ExecTranRakutenIdInput.php');
include_once('com/gmo_pg/client/tran/ExecTranRakutenId.php');
include_once('com/gmo_pg/client/input/SearchTradeMultiInput.php');
include_once('com/gmo_pg/client/tran/SearchTradeMulti.php');

/**
 * 楽天ID決済
 * Class Rakuten
 */
class Rakuten extends BasePayment
{
	/**
	 * 処理区分 即時売上
	 */
	const JobCD_CAPTURE = 'CAPTURE';

	/**
	 * 処理区分 仮売上
	 */
	const JobCD_AUTH = 'AUTH';

	/**
	 * 楽天ID決済のpayType
	 */
	const payType = 18;

	/**
	 * shopID
	 * @var
	 */
	public $shopId = NULL;

	/**
	 * shopPass
	 * @var
	 */
	public $shopPass = NULL;

	/**
	 * JobCD 処理区分
	 * @var string
	 */
	public $jobCD = 'CAPTURE';

	/**
	 * 支払い結果
	 * @var null
	 */
	public $payResult = null;
	

	/**
	 * 取引の追加
	 * @param string $orderId
	 * @param int $amount
	 * @param int $tax
	 * @return array OrderID,AccessID,AccessPass
	 * @throws Exception
	 */
	public function add($orderId = '', $amount = 0, $tax = 0)
	{
		$input = new EntryTranRakutenIdInput();
		$input->setShopID($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setAmount($amount);
		$input->setOrderID($orderId);
		$input->setJobCd($this->jobCD);
		$input->setTax($tax);
		$entry = new EntryTranRakutenId();
		$result = $entry->exec($input);
		if ($result->getErrList()) {
			$this->logger->error($result->getErrList());
			throw new Exception('取引の登録に失敗しました。時間を置いてから再度実行してください。');
		}
		return [
			'OrderID' => $orderId,
			'AccessID' => $result->getAccessId(),
			'AccessPass' => $result->getAccessPass()
		];
	}

	/**
	 * 決済スタートURLの取得
	 * @param array $access add()のresult
	 * @param string $itemId 商品ID
	 * @param string $itemName 商品名
	 * @param string $retUrl 成功時のURL
	 * @param string $errorUrl エラー時のURL
	 * @return array
	 * @throws Exception
	 */
	public function getEntryUrl($access=[], $itemId='',$itemName='', $retUrl, $errorUrl)
	{
		$input = new ExecTranRakutenIdInput();
		$input->setShopID($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setOrderID($access['OrderID']);
		$input->setAccessID($access['AccessID']);
		$input->setAccessPass($access['AccessPass']);
		$input->setItemId($itemId);
		$input->setItemName($itemName);
		$input->setRetURL($retUrl);
		$input->setErrorRcvURL($errorUrl);
		$entry = new ExecTranRakutenId();
		$result = $entry->exec($input);
		if($result->getErrList())
		{
			//TODO: 適切なエラーメッセージ
			throw new Exception('エラーが発生しました。');
		}
		$resultData = [
			'AccessID' => $result->getAccessID(),
			'token' => $result->getToken(),
			'startURL' => $result->getStartURL(),
			'startLimitData' => $result->getStartLimitDate(),
			'orderID' => $access['OrderID'],
		];
		return $resultData;
	}

	/**
	 * 決済の戻り先URLから取得した値から決済結果を取得
	 * @return bool
	 * @throws Exception
	 */
	public function checkHash()
	{
		$this->checkInit();
		if(! isset($_GET['ShopID'])
			|| ! $_GET['ShopID']
			|| ! isset($_GET['OrderID'])
			|| ! $_GET['OrderID']
			|| ! isset($_GET['c'])
			|| ! $_GET['c']
		)
		{
			return false;
		}

		if(isset( $_GET['ErrInfo']) && $_GET['ErrInfo'])
		{
			//TODO: エラー LOG保存
			return false;
		}

		if($_GET['c'] == $this->createCheckHash($_GET['OrderID']))
		{
			return true;
		}
		return false;
	}

	public function createCheckHash($orderId='')
	{
		$text = $this->shopId . $orderId . $this->shopPass;
		return md5($text);
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
		$status = $result->getStatus();
		if($status && isset($this->statusList[$status]))
		{
		 	return $this->statusList[$status];
		}
		return $status;
	}

}