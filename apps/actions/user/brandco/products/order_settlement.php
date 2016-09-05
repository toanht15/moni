<?php
AAFW::import('jp.aainc.lib.base.aafwActionPluginBase');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.settlement');

/**
 * 注文決済
 * Class orderSettlement
 */
class order_settlement extends BrandcoPOSTActionBase
{
	public $CsrfProtect = true;
	protected $ContainerName = 'orderSettlement';
	/**
	 * option
	 * @var array
	 */
	public $NeedOption = array();

	/**
	 * access_code
	 * @var null
	 */
	private $_accessCode = NULL;

	/**
	 * products.id
	 * @var int
	 */
	private $_productId = 0;

	/**
	 * users.id
	 * @var int
	 */
	private $_userId = 0;

	/**
	 * 注文情報
	 * @var array
	 */
	private $_orderData = [];


	/**
	 * validate
	 * @return bool
	 */
	public function validate()
	{
		return true;
	}

	/**
	 * action
	 * @return string
	 */
	public function doAction()
	{
		try {
			$this->_setUserId();
			$this->_setJsonFormat();
			$this->_setProductId();
			$this->_setAccessCode();
			$this->_getOrderData();
			$this->settlement();
			$this->updateShippingAddress();
			return $this->viewJson();
		} catch(Exception $e)
		{
			aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
			$this->Data['json_data']['status'] = 'ng';
			$this->Data['json_data']['error'][] = $e->getMessage();
			return $this->viewJson();
		}
	}

	/**
	 * jsonの表示 $data['json_data']
	 * @return string
	 */
	private function viewJson()
	{
		header("Content-Type: text/javascript; charset=utf-8");
		return 'user/brandco/products/order.php';
	}


	/**
	 * 決済
	 * @return bool
	 */
	private function settlement()
	{
		$obj = new Settlement();
		$result = $obj->execSettlement($this->_userId, $this->_accessCode,  $this->_orderData);
		if($result)
		{
			//処理成功
			$this->Data['json_data']['redirect_url'] = ''; //リダイレクト先
			$this->setSession(preOrder::SESSION_KEY_ACCESS_CODE,"");
			return true;
		}
		$this->Data['json_data']['status'] = 'ng';
		$this->Data['json_data']['error'] = $obj->errors;
		return false;
	}

	/**
	 * 注文情報の取得
	 */
	private function _getOrderData()
	{
		//access_codeがない。
		if(! $this->_accessCode){
			$this->Data['json_data']['status'] = 'ng';
			$this->Data['json_data']['error']['message'] = '注文処理実行時間を超過しました'; // TODO:文言検討
			return;
		}

		//注文情報の取得
		$obj = new preOrder();
		$data = $obj->getPageViewDetail($this->_productId, $this->_accessCode);
		if(isset($data['product']) && ! $obj->validateErrors)
		{
			$this->_orderData = $data;
			return;
		}

		//注文確定前に売り切れが発生した。 $obj->getPageViewDetailデータ取得時に判定
		if($obj->validateErrors)
		{
			if(! isset($this->Data['order_edit_url']) || ! $this->Data['order_edit_url'])
			{
				$this->setEditOrderUrl();
			}
			$this->viewPath = 'redirect: ' . $this->Data['order_edit_url'];
			return ;
		}
	}

	/**
	 * productIdの取得と設定
	 */
	private function  _setProductId()
	{
		if(isset($this->GET['exts'][0]) && $this->GET['exts'][0])
		{
			$this->_productId = $this->GET['exts'][0];
		}
	}

	/**
	 * accessCodeの取得
	 */
	private function _setAccessCode()
	{
		$accessCode = $this->getSession(preOrder::SESSION_KEY_ACCESS_CODE);
		if(isset($accessCode[$this->_productId]))
		{
			$this->_accessCode = $accessCode[$this->_productId];
		}
	}

	/**
	 * json 雛形取得
	 * @return array
	 */
	private function _setJsonFormat()
	{
		$this->Data['json_data'] =  [
			'status' => 'ok',
			'data' => [
				'redirect_url' => null
			],
			'error' => []
		];
	}


	/**
	 * 修正ページへのURL設定
	 */
	private function setEditOrderUrl()
	{
		$this->Data['order_edit_url'] = 'https://'.$this->getBrand()->directory_name.'/products/order_edit/'. $this->_productsId.'/';
	}

	

	private function _setUserId()
	{
		$userId = $this->getSession('pl_monipla_userId');
		if($userId)
		{
			$this->_userId = (int)$userId;
		}
	}

	private function updateShippingAddress(){
		try{
			$shippingAddressObject = $this->convertShippingAddress();
			$userInfo = (object)$this->getSession('pl_monipla_userInfo');
			$shippingAddressManager = new ShippingAddressManager($userInfo);
			$shippingAddressManager->setAddress($shippingAddressObject);
		}catch (Exception $e){
			aafwLog4phpLogger::getHipChatLogger()->error('allied_id : '.$userInfo->id." 決済後の住所更新エラー念の為にユーザーの決済状況を確認する");
		}
	}

	public function convertShippingAddress(){
		$shippingAddressObject = new stdClass();
		foreach (ShippingAddressManager::$AddressParams as $key => $param){
			$shippingAddressObject->$key = $this->_orderData[$key];
		}
		$shippingAddressObject->prefId = $this->_orderData['prefId'];
		return $shippingAddressObject;
	}
}
