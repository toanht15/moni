<?php
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.ShippingAddressService');

/**
 * CoreのShippingAddressを操作するクラス
 * Class ShippingAddressManager
 */
class ShippingAddressManager {
    private $userInfo    = null;
    private $moniplaCore = null;
    private $shippingAddressService;

    public static $AddressParams = array(
        'lastName' => 'last_name',
        'firstName' => 'first_name',
        'lastNameKana' => 'last_name_kana',
        'firstNameKana' => 'first_name_kana',
        'zipCode1' => 'zip_code1',
        'zipCode2' => 'zip_code2',
        'prefId' => 'pref_id',
        'address1' => 'address1',
        'address2' => 'address2',
        'address3' => 'address3',
        'telNo1' => 'tel_no1',
        'telNo2' => 'tel_no2',
        'telNo3' => 'tel_no3'
    );

    public function __construct($userInfo, $moniplaCore = null) {
        $this->userInfo = $userInfo;
        if ( $moniplaCore == null) $moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        $this->moniplaCore = $moniplaCore;
        if (!$this->shippingAddressService) $this->shippingAddressService = ShippingAddressService::getInstance();
    }

    public function getAddress() {
        return $this->getShippingAddress();
    }

    public function setAddress($value) {

        $address = $this->getShippingAddress();
        $thriftParams['userId'] = $this->userInfo->id;
        foreach (self::$AddressParams as $param => $paramValue) {
            // 値があれば更新する
            $thriftParams[$param] = ($value->$param) ? $value->$param : $address->$param;
        }
        $this->moniplaCore->updateAddress(array('class' => 'Thrift_Address', 'fields' => $thriftParams));
        $this->shippingAddressService->updateAddress($thriftParams);

        return true;
    }

    /**
     * ShippingAddress情報取得
     * @return mixed
     */
    public function getShippingAddress() {
        return $this->moniplaCore->getShippingAddress(array(
            'class' => 'Thrift_Address',
            'fields' => array('userId' => $this->userInfo->id,)
        ))->address;
    }
}