<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.orderHistory');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
/**
 * 注文確認
 * Class orderComfirm
 */
class order_confirm  extends BrandcoGETActionBase
{
	public $NeedOption = array();

	/**
	 * returnで指定するviewのパス
	 * @var string
	 */
	private $_viewPath = 'user/brandco/products/order_confirm.php';

	/**
	 * 注文情報の取得成功時に使用するviewのパス
	 * @var string
	 */
	private $_successPageView = 'user/brandco/products/order_confirm.php';

	/**
	 * 注文情報取得失敗時に使用するパス
	 * @var string
	 */
	private $_errorPageView = 'user/brandco/products/order_confirm_error.php';

	/**
	 * access_code
	 * @var null
	 */
	private $_accessCode = NULL;

	/**
	 * products.id
	 * @var int
	 */
	private $productId = 0;

	/**
	 * users.id
	 * @var int
	 */
	private $userId = 0;

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
		$this->_setProductId();
		$this->setUserId();
		//ログインしてから表示
		if(! $this->isLogin())
		{
			return $this->doLoginRidirect();
		}
		$this->_setAccessCode();
		$this->setProductsUrl();
		$this->setEditOrderUrl();
		$this->_getData();
		if( !$this->Data['order'] ){
			return $this->_errorPageView;
		}
		$this->_setSettlementUrl();
		$this->_setCpUrl();
		$this->getLastOrder();

		$products = new ProductsRepository();
		$product = $products->getById($this->productId);

		/** @var CpAction $cpAction */
		$cpAction = $this->getModel(CpActions::class)->findOne($product['cp_action_id']);
		$this->Data['is_opening_flg'] = $cpAction->isOpeningCpAction();
		if($this->Data['is_opening_flg']){
			$this->Data['join_url'] = Util::rewriteUrl("messages", "join");
		}else{
			$this->Data['join_url'] = Util::rewriteUrl("messages", "api_execute_payment_action");
		}


		$this->Data['cp_id'] = $product['cp_id'];
		$this->Data['cp_action_id'] = $product['cp_action_id'];
		$cpUser = $this->getModel(CpUsers::class)->findOne([
			'cp_id' =>  $product['cp_id'],
			'user_id' => $this->userId
		]);
		$this->Data['cp_user_id'] = $cpUser->id;
		$this->Data['beginner_flg'] = $this->isBeginner($this->userId) ? "1" : "0";

		$this->Data['isFinishedPaymentAction'] = $this->isFinishedPaymentAction($product['cp_id'], $product['cp_action_id'], $this->getSession('pl_monipla_userId'));

		return $this->viewPage();
	}

	/**
	 * ログイン促し。
	 * @return string
	 */
	private function doLoginRidirect()
	{
		//ログイン後このページ戻ってきてほしい
		$this->setSession('loginRedirectUrl', Util::rewriteUrl('products', 'order_confirm',array($this->productId)));
		//ログイン画面へリダイレクト
		return 'redirect: ' .Util::rewriteUrl('my', 'login');
	}

	/**
	 * 画面表示
	 * @return string
	 */
	private function viewPage()
	{
		return $this->_viewPath;
	}

	/**
	 * AccessCodeの確認
	 * 注文情報の取得
	 */
	private function _getData()
	{
		//access_codeがない。
		if(! $this->_accessCode){
			//エラ-画面を表示
			$this->_viewPath = $this->_errorPageView;
			return;
		}
		//注文情報の取得
		$obj = new preOrder();
		$data = $obj->getPageViewDetail($this->productId, $this->_accessCode);
		if(isset($data['product']) && ! $obj->validateErrors)
		{
			$this->Data['order'] = $data;
			$this->_viewPath = $this->_successPageView;
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
		//エラ-画面を表示
		$this->_viewPath = $this->_errorPageView;
	}

	private function isBeginner($userId){
		$cps = $this->getModel(Cps::class)->find(array('brand_id'=>$this->getBrand()->id));
		$cpIds = array();
		foreach ($cps as $cp){
			$cpIds[] = $cp->id;
		}
		$cpUser = $this->getModel(CpUsers::class)->findOne(array(
			'cp_id:IN'=>$cpIds,
			'user_id' => $userId
		));

		return $cpUser->id ? false : true;
	}

	/**
	 * accessCodeの取得
	 */
	private function _setAccessCode()
	{
		$accessCode = $this->getSession(preOrder::SESSION_KEY_ACCESS_CODE);
		if(isset($accessCode[$this->productId]))
		{
			$this->_accessCode = $accessCode[$this->productId];
		}
	}

	/**
	 * productIdの取得と設定
	 */
	private function  _setProductId()
	{
		if(isset($this->GET['exts'][0]) && $this->GET['exts'][0])
		{
			$this->productId = $this->GET['exts'][0];
		}
	}

	/**
	 * プロダクトURLを設定
	 */
	private function setProductsUrl()
	{
		$this->Data['products_url'] = Util::rewriteUrl('products', 'detail',array($this->productId));
	}

	/**
	 * 注文修正のURL設定
	 */
	private function setEditOrderUrl()
	{
		$this->Data['order_edit_url'] = Util::rewriteUrl('products', 'order_edit',array($this->productId));
	}

	/**
	 * 注文確定URLの設定 (ajax)
	 */
	private function _setSettlementUrl()
	{
		$this->Data['order_settlement_url'] = Util::rewriteUrl('products', 'order_settlement',array($this->productId)); 
	}

	/**
	 * キャンペーンページ URL
	 */
	private function _setCpUrl()
	{
		$this->Data['cp_url'] = Util::rewriteUrl('messages', 'thread',array($this->Data['order']['product']['cp_id']));
	}

	/**
	 * 購入履歴1件の取得
	 */
	private function getLastOrder()
	{
		$obj = new orderHistory();
		$this->Data['history'] = $obj->getLastOrder($this->userId, $this->productId);
	}

	/**
	 * users.idの取得
	 */
	private function setUserId()
	{
		$this->userId = $this->getSession('pl_monipla_userId');
	}

	/**
	 * @param $cpId
	 * @param $cpActionId
	 * @param $userId
	 * @return bool
	 */
	private function isFinishedPaymentAction($cpId,$cpActionId,$userId){
		$cp_user = $this->getModel('CpUsers')->findOne(
			array(
				'cp_id'=>$cpId,
				'user_id'=>$userId
			)
		);

		$cpUserActionStatus = $this->getModel(CpUserActionStatuses::class)->findOne(
			array(
				'cp_user_id'=>$cp_user->id,
				'cp_action_id'=>$cpActionId
			)
		);
		return $cpUserActionStatus->status ? true : false;
	}
}
