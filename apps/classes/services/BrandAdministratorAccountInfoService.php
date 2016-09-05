<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/07/15
 * Time: 15:42
 */

AAFW::import('jp.aainc.classes.services.StreamService');

class BrandAdministratorAccountInfoService extends aafwServiceBase {

    private $brandAdministratorAccountInfos;

    public function __construct() {
        $this->brandAdministratorAccountInfos = $this->getModel('BrandAdministratorAccountInfos');
    }

    /**
     * @param BrandAdministratorAccountInfo $accountInfo
     */
    public function saveAccountInfo(BrandAdministratorAccountInfo $accountInfo) {
        $this->brandAdministratorAccountInfos->save($accountInfo);
    }

    /**
     * @return mixed
     */
    public function getEmptyAccountInfo() {
        return $this->brandAdministratorAccountInfos->createEmptyObject();
    }

    /**
     * @param $brandId
     * @return mixed
     */
    public function getAccountListByBrandId($brandId) {
        return $this->brandAdministratorAccountInfos->find(array('brand_id' => $brandId));
    }

    /**
     * @param $brandId
     * @param $accountNo
     * @return mixed
     */
    public function getAccountByBrandIdAndAccountNo($brandId, $accountNo) {
        $filter = array(
            'conditions' => array(
                'brand_id'                 => $brandId,
                'administrator_account_no' => $accountNo,
            )
        );

        if ($this->brandAdministratorAccountInfos->findOne($filter)) {
            return $this->brandAdministratorAccountInfos->findOne($filter);
        } else {
            return $this->getEmptyAccountInfo();
        }
    }

    /**
     * 登録済みのアカウント数を返す
     * @param $brandId
     * @return mixed
     */
    public function countRegisteredAccountListByBrandId($brandId) {
        return $this->brandAdministratorAccountInfos->count(array('brand_id' => $brandId));
    }

    /**
     * 新規登録、更新するアカウント数を返す
     * @param array $post
     * @return int
     */
    public static function countRegisterAccount(array $post) {
        return count( preg_grep("/administrator_account_no_/", array_keys($post)) );
    }

    /**
     * @param $accountNameKey
     * @return array
     */
    public static function createValidatorForAccountName($accountNameKey) {
        $validator = array(
            $accountNameKey => array(
                'type'     => 'str',
                'length'   => 255,
            ),
        );

        return $validator;
    }

    /**
     * @param $accountMailAddressKey
     * @return array
     */
    public static function createValidatorForMailAddress($accountMailAddressKey) {
        $validator = array(
            $accountMailAddressKey => array(
                'type'      => 'str',
                'length'    => 255,
                'validator' => array('MailAddress'),
            )
        );

        return $validator;
    }

    /**
     * @param $accountTELKey
     * @return array
     */
    public static function createValidatorForTEL($accountTELKey) {
        $validator = array(
            $accountTELKey => array(
                'type'     => 'num',
            ),
        );

        return $validator;
    }

    /**
     * @param $tel_no1
     * @param $tel_no2
     * @param $tel_no3
     * @return 真偽値
     */
    public static function validateAccountTEL($tel_no1, $tel_no2, $tel_no3) {
        return self::isTEL($tel_no1 . '-' .$tel_no2 . '-' .$tel_no3);
    }

    /**
     * @param $accountName
     * @param $accountMailAddress
     * @param $tel_no1
     * @param $tel_no2
     * @param $tel_no3
     * @return bool
     */
    public static function isEmptyAccountInfoParameter($accountName, $accountMailAddress, $tel_no1, $tel_no2, $tel_no3) {
        return (
            empty($accountName) &&
            empty($accountMailAddress) &&
            empty($tel_no1) &&
            empty($tel_no2) &&
            empty($tel_no3)
        );
    }

}