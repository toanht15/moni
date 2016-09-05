<?php
set_include_path(get_include_path() . ':' . dirname(__FILE__) . '/../../../vendor/gpay_client/src/');
include_once('com/gmo_pg/client/input/AuCancelReturnInput.php');
include_once('com/gmo_pg/client/tran/AuCancelReturn.php');
include_once('com/gmo_pg/client/input/SearchTradeMultiInput.php');
include_once('com/gmo_pg/client/tran/SearchTradeMulti.php');


class BasePayment
{
	/**
	 * siteId
	 * @var null
	 */
	public $siteId = null;

	/**
	 * sitePass
	 * @var null
	 */
	public $sitePass = null;
	/**
	 * @var
	 */
	public $shopId = null;
	/**
	 * @var
	 */
	public $shopPass = null;
	/**
	 * orderId
	 * @var
	 */
	public $orderId = null;

	public $errors = null;

	/**
	 * 決済ステータス
	 * @var array
	 */
	public $statusList = [
		'UNPROCESSED' => '未決済',
		'AUTHENTICATED' => '未決済(3D 登録済)',
		'CHECK' => '有効性チェック',
		'CAPTURE' => '即時売上',
		'AUTH' => '仮売上',
		'SALES' => '実売上',
		'VOID' => '取消',
		'RETURN' => '返品',
		'RETURNX' => '月跨り返品',
		'SAUTH' => '簡易オーソリ'
	];

	public $errorList = [];

	/**
	 * エラーメッセージリスト
	 * @var array
	 */
	public $errorMessage = [
		'E11010099' => 'ご利用出来ないカードをご利用になったもしくはカードの入力情報が誤っております。再度内容を確認して登録ください。',
		'E41170002' => 'ご利用出来ないカードをご利用になったもしくはカードの入力情報が誤っております。再度内容を確認して登録ください。',
		'E41170099' => 'ご利用出来ないカードをご利用になったもしくはカードの入力情報が誤っております。再度内容を確認して登録ください。',
		'E61010002' => 'ご利用出来ないカードをご利用になったもしくはカードの入力情報が誤っております。再度内容を確認して登録ください。',
		'E91029999' => '選択いただいたお支払い方法は現在利用できない可能性があります。しばらく時間をあけて購入画面からやり直してください。',
		'E92000001' => '只今、大変込み合っていますので しばらく時間をあけて再度決済を 行ってください。',
		'E91099999' => '選択いただいたお支払い方法は現在利用できない可能性があります。しばらく時間をあけて購入画面からやり直してください。',
		'42G020000' => 'カード残高が不足しているために、決済を完了する事が出来ませんでした。',
		'42G030000' => 'カード限度額を超えているために、決決済を完了する事が出来ませんでした。',
		'42G040000' => 'カード残高が不足しているために、決済を完了する事が出来ませんでした。',
		'42G050000' => 'カード限度額を超えているために、決済を完了する事が出来ませんでした。',
		'42G120000' => 'このカードでは取引をする事が出来ません。',
		'42G300000' => 'このカードでは取引をする事が出来ません',
		'42G420000' => '暗証番号が誤っていた為に、決済を完了する事が出来ませんでした。',
		'42G440000' => 'セキュリティーコードが誤っていた為に、決済を完了する事が出来ませんでした。',
		'42G450000' => 'セキュリティーコードが入力されていない為に、決済を完了する事が出来ませんでした。',
		'42G540000' => 'このカードでは取引をする事が出来ません。',
		'OTHER' =>	'決済処理に失敗しました。申し訳ありませんが、入力内容に誤りが無いかをご確認のうえ、再度やり直してください。'
	];


	/**
	 * shopIDの設定
	 * @param string $shopId
	 */
	public function setShopId($shopId = '')
	{
		$this->shopId = $shopId;
	}

	/**
	 * shopPassの設定
	 * @param string $shopPass
	 */
	public function setShopPass($shopPass = '')
	{
		$this->shopPass = $shopPass;
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
	 * shopの情報が保存されているかどうかの確認
	 * @throws Exception
	 */
	public function checkInit()
	{
		if (!$this->shopId) {
			throw new Exception('shopIdの指定がありません');
		}
		if (!$this->shopPass) {
			throw new Exception('shopPassの指定がありません');
		}
	}

	/**
	 * siteIdの設定
	 * @param string $siteId
	 */
	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
	}

	/**
	 * sitePassの設定
	 * @param string $pass
	 */
	public function setSitePass($pass)
	{
		$this->sitePass = $pass;

	}

	/**
	 * キャンセル
	 * @param string $accessId
	 * @param string $accessPass
	 * @param string $orderId
	 * @param int $amount
	 * @param int $tax
	 * @return bool
	 */
	public function cancel($accessId = '', $accessPass = '', $orderId = '', $amount = 0, $tax = 0)
	{
		$this->checkInit();
		$input = new AuCancelReturnInput();
		$input->setShopID($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setAccessID($accessId);
		$input->setAccessPass($accessPass);
		$input->setOrderID($orderId);
		$input->setCancelAmount($amount + $tax);
		$entry = new AuCancelReturn();
		$result = $entry->exec($input);
		if (isset($result->errList) && $result->errList) {
			throw new Exception('キャンセルに失敗しました。');
			return false;
		}
		return true;
	}

	/**
	 * @param int $payType
	 * @param string $orderId gmo order id  orders.id _  uniqid()
	 * @return array|null
	 */
	public function getStatus($payType, $orderId)
	{
		$this->checkInit();
		$input = new SearchTradeMultiInput();
		$input->setShopId($this->shopId);
		$input->setShopPass($this->shopPass);
		$input->setPayType($payType);
		$input->setOrderId($orderId);
		$entry = new SearchTradeMulti();
		$result = $entry->exec($input);
		if ($result->errList) {
			aafwLog4phpLogger::getDefaultLogger()->error($result->getErrList());
			$this->errors[__FUNCTION__] = '状態の取得に失敗しました。';
			return null;
		}
		$data = [
			'status' => $result->getStatus(), //Status
			'process_date' => $result->getProcessDate(), //ProcessDate 処理日時
			'access_id' => $result->getAccessId(),//AccessID
			'access_pass' => $result->getAccessPass(), //AccessPass
			'amount' => $result->getAmount(),//Amount 利用金額
			'tax' => $result->getTax(), //Tax 税金送料
			'card_no' => $result->getCardNo(),//CardNo　カード番号 下4桁
			'CvsCode' => $result->getCvsCode(),//CvsCode コンビニコード
			'PaymentTerm' => $result->getPaymentTerm() //支払い期限
		];
		return $data;
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
