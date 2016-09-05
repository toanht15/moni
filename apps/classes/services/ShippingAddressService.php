<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.PrefectureService');
AAFW::import('jp.aainc.classes.services.UserService');

class ShippingAddressService extends aafwServiceBase {
    /** @var aafwEntityStoreBase $shipping_address  */
    protected $shipping_address;
    private $userService;
    private static $_instance = null;

    public function __construct() {
        $this->shipping_address = $this->getModel("ShippingAddresses");
        if (!$this->userService) $this->userService = UserService::getInstance();
    }

    public function getPrefectureByUserId($user_id) {

        $prefecture_id = $this->getShippingAddressByUserId($user_id)->pref_id;
        $prefecture = PrefectureService::getInstance()->getPrefectureByPrefId($prefecture_id);
        return $prefecture;
    }

    public function getShippingAddressByUserId($user_id) {

        $filter = array(
            'conditions' => array(
                'user_id' => $user_id,
            ),
        );

        $shipping_address = $this->shipping_address->findOne($filter);
        return $shipping_address;
    }

    public function updateAddress($thriftParams) {
        $user = $this->userService->getUserByMoniplaUserId($thriftParams['userId']);
        $shipping_address = $this->getShippingAddressByUserId($user->id);
        if(!$shipping_address) {
            $shipping_address = $this->createEmptyShippingAddress();
            $shipping_address->user_id = $user->id;
        }

        foreach($thriftParams as $key=>$value) {
            $key = ShippingAddressManager::$AddressParams[$key];
            $shipping_address->$key = $value;
        }
        $this->createShippingAddress($shipping_address);
    }

    public function createShippingAddress($shipping_address) {
        $this->shipping_address->save($shipping_address);
    }

    public function createEmptyShippingAddress() {
        return $this->shipping_address->createEmptyObject();
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function deleteShippingAddressByUserId($user_id) {
        $address = $this->getShippingAddressByUserId($user_id);
        if (!$address) {
            return;
        }
        $this->shipping_address->deletePhysical($address);
    }
}