<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

/**
 * アイテム購入画面(決済入力フォーム兼用）
 * Class detail
 */
class order_edit extends BrandcoGETActionBase
{
	public $NeedOption = array();

	/**
	 * products.id
	 * @var int
	 */
	private $_productId = 0;

	/**
	 * access code
	 * @var
	 */
	private $_accessCode = null;

	/**
	 * validate
	 * @return bool
	 */
	public function validate()
	{
		return true;
	}

	/**
	 * 実行
	 * @return string
	 */
	public function doAction()
	{
		$this->_setProductId();
		if(! $this->_productId)
		{
			return 404;
		}
		if (! $this->_setProduct()) {
			return 404;
		}
		if(! $this->_setOrderData())
		{
			//注文情報が取れない //有効期限切れもしくは不正アクセス
			return $this->viewOderErrorPage();
		}
		$this->Data['cancelUrl'] = Util::rewriteUrl('campaigns',$this->Data['product']['detail']['cp_id']);
		$this->Data['orderUrl'] = Util::rewriteUrl('products', 'order',array($this->GET['exts']['0']));
		$this->_setConvenienceStoreList();
		return $this->viewPage();
	}

	/**
	 * order access error用エラー画面
	 * @return string
	 */
	private function viewOderErrorPage()
	{
		//注文情報が取れなかった場合のエラー表示
		$this->setProductsUrl();
		return 'user/brandco/products/order_confirm_error.php';
	}

	/**
	 * プロダクトURLを設定
	 */
	private function setProductsUrl()
	{
		$this->Data['products_url'] = Util::rewriteUrl('products', 'detail',array($this->_productId));
	}

	/**
	 * コンビニ決済一覧の設定
	 */
	private function _setConvenienceStoreList()
	{
		$obj = new preOrder();
		$this->Data['convenienceStoreList'] = $obj->convenienceStoreList;
	}


	/**
	 * 表示のための情報を取得
	 * @return bool
	 */
	private function _setOrderData()
	{
		$this->_setAccessCode();
		$obj = new preOrder();
		$order = $obj->getDetail($this->_productId, $this->_accessCode);
		if($order)
		{
			$this->setViewDataFormat($order);
			$this->Data['order'] = $order;
			$this->Data['validateErrors'] = $obj->validateErrors;
			return true;
		}
		return false;
	}

	/**
	 * detailのテンプレート兼用とするためフォーマットを揃える
	 * @param $order
	 */
	private function setViewDataFormat($order)
	{
		$this->setPrefList();
		$this->Data['address'] = [
			'socialAccount' => '',
			'userId' => '',
			'mailAddress' => '',
			'firstName' => $order['firstName'],
			'lastName' => $order['lastName'],
			'firstNameKana' => $order['firstNameKana'],
			'lastNameKana' => $order['lastNameKana'],
			'zipCode1' => $order['zipCode1'],
			'zipCode2' => $order['zipCode2'],
			'prefId' => $order['prefId'],
			'address1' => $order['address1'],
			'address2' => $order['address2'],
			'address3' => $order['address3'],
			'telNo1' => $order['telNo1'],
			'telNo2' => $order['telNo2'],
			'telNo3' => $order['telNo3']
		];
		$this->Data['payType'] = $order['payType'];
		$this->Data['convenienceCode'] = $order['convenienceCode'];
		$this->Data['cardName'] = $order['cardName'];
		$this->Data['cardNumber'] = $order['cardNumber'];
		$this->Data['cardExpirationMonth'] = $order['cardExpirationMonth'];
		$this->Data['securityCode'] = $order['securityCode']; //修正登録でもここは再入力させたい
	}

	/**
	 * 商品情報の取得
	 * @return bool
	 */
	private function _setProduct()
	{
		if (! $this->_productId) {
			return false;
		}
		$Product = new ProductsRepository();
		$this->Data['product'] = $Product->getDetail($this->_productId);
		$this->Data['isLogin'] = $this->isLogin();
		return true;
	}

	/**
	 * viewの設定
	 * @return string
	 */
	private function viewPage()
	{
		$this->Data['isLogin'] = $this->isLogin();
		return 'user/brandco/products/detail.php';
	}

	/**
	 * 都道府県リスト取得
	 * @return array
	 */
	private function setPrefList()
	{
		$Product = new ProductsRepository();
		$this->Data['prefList'] = $Product->getPrefList();
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

}