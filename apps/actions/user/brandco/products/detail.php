<?php

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
 * アイテム購入画面(決済入力フォーム兼用）
 * Class detail
 */
class detail extends BrandcoGETActionBase
{
    public $NeedOption = array();

    /**
     * users.id
     * @var int
     */
    private $userId = 0;

    /**
     * products.id
     * @var int
     */
    private $productId = 0;


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
        $this->setUserId();
        $this->setUserData();
        if (!$this->_setProduct()) {
            return 404;
        }

        //デモチェック
        if ($this->Data['product']['detail']['cp_status'] == Cp::STATUS_DEMO && $this->getSession("demo_token_".$this->Data['product']['detail']['cp_id']) != hash("sha256", $this->Data['product']['detail']['cp_created_at'])) {
            return 404;
        }
        
        /** @var Cp $cp */
        $cp = $this->getModel(Cps::class)->findOne($this->Data['product']['detail']['cp_id']);
        //常設キャンペーンじゃない場合は、このモジュールまでステップが進んでる事が必要
        if(!$cp->isPermanent()) {
            if(!$this->getSession('pl_monipla_userId')){
                return 404;
            }
            if(!$this->isReachThisPaymentModule(
                $cp->id,
                $this->Data['product']['detail']['cp_action_id'],
                $this->getSession('pl_monipla_userId'))
            ){
                return 404;
            }
        }

        $this->Data['cancelUrl'] = Util::rewriteUrl('campaigns',$this->Data['product']['detail']['cp_id']);
        $this->setConvenienceStoreList();
        $this->getOrderCount();
        $this->Data['orderUrl'] = Util::rewriteUrl('products', 'order',array($this->GET['exts']['0']));
        return $this->viewPage();
    }

    /**
     * 注文個数
     */
    private function getOrderCount()
    {
        $this->Data['order_count'] = [];
        if(isset($this->GET['order_count'])){
            $this->Data['order_count'] = $this->GET['order_count'];
        }
    }

    /**
     * コンビニ決済一覧の設定
     */
    private function setConvenienceStoreList()
    {
        $obj = new preOrder();
        $this->Data['convenienceStoreList'] = $obj->convenienceStoreList;
    }


    /**
     * 表示する情報を取得
     */
    private function setUserData()
    {
        //loginしている場合
        if ($this->isLogin()) {
            $this->setAddress();
            $this->Data['prefList'] = $this->getPrefList();
            return;
        }
        $this->setAnyoneData();
    }

    /**
     * ログインしていない人の初期情報を設定
     */
    private function setAnyoneData()
    {
        $this->Data['address'] = $this->getDefaultAddress();
        //ログイン後のリダイレクトURL指定
        $this->setSession('loginRedirectUrl', '/products/detail/' . $this->GET['exts'][0]);
        $this->Data['prefList'] = $this->getPrefList();
    }

    /**
     * 住所情報の初期値
     * @return array
     */
    private function getDefaultAddress()
    {
        return [
            'socialAccount' => null,
            'userId' => null,
            'mailAddress' => '',
            'firstName' => null,
            'lastName' => null,
            'firstNameKana' => null,
            'lastNameKana' => null,
            'zipCode1' => null,
            'zipCode2' => null,
            'prefId' => 13,
            'address1' => null,
            'address2' => null,
            'address3' => null,
            'telNo1' => null,
            'telNo2' => null,
            'telNo3' => null
        ];
    }

    /**
     * 配送先住所の設定
     * 情報は配列に変換
     * DBから取得した情報はArrayなので、それにフォーマットを揃えるため。
     * DBから直接とるようになる可能性があるためでもあり...
     */
    private function setAddress()
    {
        $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo']);
        $address = $shippingAddressManager->getShippingAddress();
        if ($address) {
            $this->Data['address'] = [
                'socialAccount' => $address->socialAccount,
                'userId' => $address->userId,
                'mailAddress' => $address->mailAddress,
                'firstName' => $address->firstName,
                'lastName' => $address->lastName,
                'firstNameKana' => $address->firstNameKana,
                'lastNameKana' => $address->lastNameKana,
                'zipCode1' => $address->zipCode1,
                'zipCode2' => $address->zipCode2,
                'prefId' => $address->prefId,
                'address1' => $address->address1,
                'address2' => $address->address2,
                'address3' => $address->address3,
                'telNo1' => $address->telNo1,
                'telNo2' => $address->telNo2,
                'telNo3' => $address->telNo3
            ];
             $this->Data['address'];
        }else{
            $this->Data['address'] = $this->getDefaultAddress();
        }
    }

    /**
     * 商品情報の取得
     * @return bool
     */
    private function _setProduct()
    {
        if (!isset($this->GET['exts']['0']) || !$this->GET['exts']['0']) {
            return false;
        }
        $this->productId = $this->GET['exts']['0'];
        $Product = new ProductsRepository();
        $this->Data['product'] = $Product->getDetail($this->productId);
        $this->Data['isLogin'] = $this->isLogin();
        if ($this->Data['product']) {
            return true;
        }
        return false;
    }

    /**
     * viewの設定
     * @return string
     */
    private function viewPage()
    {
        return 'user/brandco/products/detail.php';
    }

    /**
     * 都道府県リスト取得
     * @return array
     */
    private function getPrefList()
    {
        $Product = new ProductsRepository();
        return $Product->getPrefList();
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
        if($this->userId)
        {
            $this->Data['isLogin'] = true;
        }
    }

    /**
     * ユーザーがこの決済モジュールのステップまで進んでいるかチェックする
     * cp_user_action_statusにデータがあるかどうかで判断
     * @param $cpId
     * @param $cpActionId
     * @param $userId
     * @return bool
     */
    private function isReachThisPaymentModule($cpId,$cpActionId,$userId){
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
        return $cpUserActionStatus->id ? true : false;
    }
}

/*
#  テスト環境データ作成用
INSERT INTO `brand_sites` (`brand_id`, `gmo_site_id`, `gmo_site_pass`, `created_at`) VALUES ('1', 'tsite00021399', 'bmaw3q27', NOW());
INSERT INTO  `brand_shops` (`brand_site_id`, `gmo_shop_id`, `gmo_shop_pass`) VALUES ('1', 'tshop00023166', 'ut3ybtpk');
INSERT INTO `products` (`title`, `image_url`, `cp_id`, `brand_shop_id`, `delivery_charge`, `inquiry_name`, `inquiry_phone`, 'inquiry_time1', 'inquiry_time2') VALUES ('健康検定ガイドブック', 'http://static-brandcotest.com/img/dummy/book.png', '1', '1', 200, '健康検定ガイドブック販売部' , '11111111111', '09:00:00', '18:00:00');
INSERT INTO `product_items` (`title`, `product_id`, `display_order`, `stock`, `sale_count`, `stock_limited`, `unit_price`) VALUES ('健康検定ガイドブック', '1', 'http://static-brandcotest.com/img/dummy/book.png', '10', '0', '0', '2160');

 */
