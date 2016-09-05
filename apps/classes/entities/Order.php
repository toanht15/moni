<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class Order extends aafwEntityBase {
    protected $_Relations = array(
        'OrderItems' => array(
            'id' => 'order_id'
        ),
        'Products' => array(
            'id' => 'product_id'
        )
    );

    /**
     * credit払いのpay_type
     */
    const payType_Credit = 0;

    /**
     * コンビニ払いのPayType
     */
    const payType_Convenience = 3;

    /**
     * 楽天ID払いのPayType
     */
    const payType_Rakuten = 18;

    public static function isNeedConfirmNumber($convenienceCode){
        $needConfirmNumberConveniences = array(
            '10001',
            '10002',
            '00003',
            '00004',
            '10005',
        );
        return in_array($convenienceCode,$needConfirmNumberConveniences);
    }

    public static function paymentNumberName($convenienceCode){
        switch ($convenienceCode){
            case '10001':
            case '10002':
            case '00003':
            case '00004':
            case '10005':
                return "お客様番号";
            case '00006':
            case '00008':
            case '00009':
                return "オンライン決済番号";
            case '00007':
                return "払込票番号";
            default:
                return "";
        }
    }
}