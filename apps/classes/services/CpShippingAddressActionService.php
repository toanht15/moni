<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class CpShippingAddressActionService extends aafwServiceBase {

    protected $shippingAddress;

    public static $arrayShipingInfo = array(
        'last_name'         => '名字',
        'first_name'        => '名前',
        'last_name_kana'    => '名字(かな)',
        'first_name_kana'   => '名前(かな)',
        'zip_code1'         => '郵便番号1',
        'zip_code2'         => '郵便番号2',
        'pref_id'           => '都道府県',
        'address1'          => '住所1',
        'address2'          => '住所2',
        'address3'          => '住所3',
        'tel_no1'           => '電話番号1',
        'tel_no2'           => '電話番号2',
        'tel_no3'           => '電話番号3'
    );

    public static $arrayBrandUserInfo = array(
        'no'              => '会員No'
    );

    public static $arrayUserInfo = array(
        'name'              => '名前(SNS)'
    );

    public function __construct() {
        $this->shippingAddress = $this->getModel('CpShippingAddressActions');
    }

    /**
     * CPアクションIDより配送情報アクションを取得
     * @param $cp_action_id
     */
    public function getCpShippingAddressAction($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            ),
        );
        $cp_shipping_address_action = $this->shippingAddress->findOne($filter);

        return $cp_shipping_address_action;
    }

    /**
     * @param $shipping_address_info
     * @param $prefectures
     * @return array
     */
    public function getCpShippingAddressCSVInfo($shipping_address_info, $prefectures) {
        $data_csv = array();

        foreach(self::$arrayBrandUserInfo as $key => $value){
            $data_csv[$key] = $shipping_address_info[$key] ? $shipping_address_info[$key] : '-';
            if( $shipping_address_info[$key] != '-'){
                $data_csv[$key] =  intval($shipping_address_info[$key]);
            }

        }
        foreach(self::$arrayUserInfo as $key => $value){
            $data_csv[$key] = $shipping_address_info[$key];
        }
        foreach(self::$arrayShipingInfo as $key => $value){
            if($key == 'pref_id'){
                $data_csv[$key] = $prefectures[$shipping_address_info[$key]];
            }else{
                $data_csv[$key] = $shipping_address_info[$key];
            }
        }

        return $data_csv;
    }

    public function getShippingAddressCSVHeader() {
        return array_merge(array_values(self::$arrayBrandUserInfo), array_values(self::$arrayUserInfo), array_values(self::$arrayShipingInfo));
    }
}
