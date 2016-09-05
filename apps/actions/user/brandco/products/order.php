<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.orderHistory');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');


/**
 * 注文(仮）
 * Class Order
 */
class Order extends BrandcoPOSTActionBase
{
	protected $ContainerName = 'save_pre_order';
	public $CsrfProtect = true;
	/**
	 * need option
	 * @var array
	 */
	public $NeedOption = array();

	/**
	 * error list
	 * @var array
	 */
	public $errors = [];


	private $_productsId = 0;


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
		$this->_getProductId();
		$this->save();
		return $this->viewJson();
	}

	/**
	 * jsonの表示
	 * @return string
	 */
	private function viewJson()
	{
		header("Content-Type: text/javascript; charset=utf-8");
		return 'user/brandco/products/order.php';
	}

	/**
	 * 注文情報の一時保存
	 * access_codeのsession保存
	 * 未ログインの場合はリダイレクト先URLを保存ログイン処理へ遷移
	 */
	private function save()
	{
		$obj = new preOrder();
		$obj->validate($this->POST);
		$this->errors = $obj->validateErrors;
		$data = $this->_getSaveDataFormat();
		if ($this->errors) {
			$data['status'] = 'ng';
			$data['error'] = $this->errors;
			$this->Data['json_data'] = $data;
			return;
		}
		$access_code = $obj->save( $this->_productsId, $this->POST);
		$this->setSession(preOrder::SESSION_KEY_ACCESS_CODE, [$this->_productsId=>$access_code]);
		$data['redirect_url'] = Util::rewriteUrl('products', 'order_confirm',array($this->_productsId));
		$this->Data['json_data'] = $data;
	}

	/**
	 * productIdの取得
	 * @return int
	 */
	private function _getProductId()
	{
		if(isset($this->POST['product_id']) && $this->POST['product_id'])
		{
			$this->_productsId =  $this->POST['product_id'];
			return $this->_productsId;
		}
		return 0;

	}

	/**
	 * json 雛形取得
	 * @return array
	 */
	private function _getSaveDataFormat()
	{
		return [
			'status' => 'ok',
			'data' => [
				'redirect_url' => null,
			],
			'error' => []
		];
	}
}
