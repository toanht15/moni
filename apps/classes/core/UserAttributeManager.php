<?php
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.UserAttributeService');
AAFW::import('jp.aainc.classes.services.UserSearchInfoService');

/**
 * CoreのUserAttributesを操作するクラス
 * Class UserAttributeManager
 */
class UserAttributeManager {
    private $userInfo    = null;
    private $moniplaCore = null;
    private $userAttributeService;
    private $userSearchInfoService;
    private $pageSettings = null;

    const SEX       = -1;
    const BIRTH_DAY = -2;
    const SEX_REAL_ID = 1;
    const BIRTH_DAY_REAL_ID = 2;
    public function __construct($userInfo, $moniplaCore = null, $pageSettings = null) {
        $this->userInfo = $userInfo;
        if ( $moniplaCore == null) $moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        $this->moniplaCore = $moniplaCore;
        if (!$this->userAttributeService) $this->userAttributeService = UserAttributeService::getInstance();
        if (!$this->userSearchInfoService) $this->userSearchInfoService = UserSearchInfoService::getInstance();
        $this->pageSettings = $pageSettings;
    }

    public function getSex ($attributes = null) {
        if ($attributes === null) {
            return $this->getAttribute(self::SEX);
        } else {
            return $attributes[self::SEX_REAL_ID];
        }
    }

    public function setSex($value){
        if (!is_null($value)) {
            return $this->setAttribute(self::SEX, $value);
        }
    }

    public function setBirthDay($birthday_y, $birthday_m, $birthday_d) {
        if (!is_null($birthday_y) && !is_null($birthday_m) && !is_null($birthday_d)) {
            $birthday = $birthday_y . '-' . sprintf('%02d', $birthday_m) . '-' . sprintf('%02d', $birthday_d);
            return $this->setAttribute(self::BIRTH_DAY, $birthday);
        }
    }

    public function getBirthDay($attributes = null) {
        if ($attributes === null) {
            return $this->getAttribute(self::BIRTH_DAY);
        } else {
            return $attributes[self::BIRTH_DAY_REAL_ID];
        }
    }

    public function getAttributes() {
        $result = $this->moniplaCore->getUserAttributes(array(
            'class' => 'Thrift_UserAttributeQuery',
            'fields' => array (
                'socialAccount' => $this->createSocialAccount(),
            )
        ));
        if ($result->result->status != Thrift_APIStatus::SUCCESS) {
            throw new aafwException($result->result->errors[0]->message);
        }
        $values = array();
        foreach ($result->userAttributeList as $value) {
            $values[$value->masterId] = $value->value;
        }
        return $values;
    }

    public function convertValue ($value) {
        if(is_object($value)) $value = (array)$value;
        return isset($value['value']) ? $value['value'] : $value;
    }

    public function getAttribute($key) {
        $result = $this->moniplaCore->getUserAttributes(array(
            'class' => 'Thrift_UserAttributeQuery',
            'fields' => array (
                'masterId' => $key,
                'socialAccount' => $this->createSocialAccount(),
            )
        ));
        if ($result->result->status != Thrift_APIStatus::SUCCESS) {
        }
        $values = json_decode($result->userAttributeList[0]->value);
        return $this->convertValue($values);
    }

    public function setAttribute($key,$value){
        if     (is_scalar($value))                $value = array('value' => $value );
        elseif (get_class($value) == 'stdClass')  $value = (array)$value;
        elseif (is_object($value))                throw new Exception('オブジェクトを送信することは出来ません');

        $account = $this->createSocialAccount();
        if ( !$account['fields']['socialMediaAccountID'] ) return;
        $result = $this->moniplaCore->addUserAttributes(array(
            'class' => 'Thrift_AddUserAttribute',
            'fields' => array(
                'socialAccount' => $account,
                'masterId' => $key,
                'value' => json_encode($value),
            )));
        $this->userAttributeService->addUserAttributes($account['fields']['socialMediaAccountID'],$key,json_encode($value));
        $this->userSearchInfoService->addUserAttributes($account['fields']['socialMediaAccountID'],$key,$value['value'],$this->pageSettings);
//        if ($result->status != Thrift_APIStatus::SUCCESS) {
//            throw new aafwException($result->result->errors[0]->message);
//        }
        return $result;
    }

    public function createSocialAccount(){
        if($this->userInfo->class && $this->userInfo->fields) return $this->userInfo;
        return array(
            'class' => 'Thrift_SocialAccount',
            'fields' => array (
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $this->userInfo->id,
                'name' => $this->userInfo->name,
            ),
        );
    }
}