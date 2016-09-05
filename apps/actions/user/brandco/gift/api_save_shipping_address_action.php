<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.services.GiftProductConfigService');
AAFW::import('jp.aainc.classes.services.GiftMessageService');

class api_save_shipping_address_action extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    public $NeedUserLogin = true;
    public $CsrfProtect = true;
    protected $ContainerName = 'api_save_shipping_address_action';
    protected $AllowContent = array('JSON');
    protected $gift_message_service;
    protected $gift_message;
    const REQUIRE_FLG = 1;

    public function validate() {

        $validatorDefinition = array(
            'lastName'        => array('required' => 1, 'type' => 'str', 'length' => 45),
            'firstName'       => array('required' => 1, 'type' => 'str', 'length' => 45),
            'lastNameKana'    => array('required' => 1, 'type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
            'firstNameKana'   => array('required' => 1, 'type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
            'zipCode1'         => array('required' => 1, 'type' => 'str', 'regex' => '/^\d{3}$/' ),
            'zipCode2'         => array('required' => 1, 'type' => 'str', 'regex' => '/^\d{4}$/'),
            'prefId'          => array('required' => 1, 'type' => 'num'),
            'address1'        => array('required' => 1, 'type' => 'str', 'length' => 255),
            'address2'        => array('required' => 1, 'type' => 'str', 'length' => 255),
            'address3'        => array('type' => 'str', 'length' => 255),
            'telNo1'          => array('required' => 1, 'type' => 'num', 'regex' => '/^\d{2,4}$/'),
            'telNo2'          => array('required' => 1, 'type' => 'num', 'regex' => '/^\d{2,4}$/'),
            'telNo3'          => array('required' => 1, 'type' => 'num', 'regex' => '/^\d{2,4}$/'),
        );

        /** @var GiftMessageService $gift_message_service */
        $this->gift_message_service  = $this->createService('GiftMessageService');
        $this->gift_message          = $this->gift_message_service->getGiftMessageByCode($this->gift_message_id, $this->param_hash);

        if (!$this->gift_message || $this->gift_message->receiver_user_id) {
            $json_data = $this->createAjaxResponse("ng", array(), array(), '');
            $this->assign('json_data', $json_data);
            return false;
        }

        /** @var GiftProductConfigService $gift_product_config_service */
        $gift_product_config_service = $this->createService('GiftProductConfigService');
        $gift_product_config         = $gift_product_config_service->getGiftProductConfig($this->gift_message->cp_gift_action_id);

        //名前不要
        if($gift_product_config->postal_name_flg != self::REQUIRE_FLG){
            unset($validatorDefinition['lastName']);
            unset($validatorDefinition['firstName']);
            unset($validatorDefinition['lastNameKana']);
            unset($validatorDefinition['firstNameKana']);
        }

        //住所不要
        if($gift_product_config->postal_address_flg != self::REQUIRE_FLG){
            unset($validatorDefinition['zipCode1']);
            unset($validatorDefinition['zipCode2']);
            unset($validatorDefinition['prefId']);
            unset($validatorDefinition['address1']);
            unset($validatorDefinition['address2']);
            unset($validatorDefinition['address3']);
        }

        //TEL不要
        if($gift_product_config->postal_tel_flg != self::REQUIRE_FLG){
            unset($validatorDefinition['telNo1']);
            unset($validatorDefinition['telNo2']);
            unset($validatorDefinition['telNo3']);
        }
        $validator = new aafwValidator($validatorDefinition);
        $validator->validate($this->POST);


        if($validator->getErrorCount()) {
            //エラー文言まとめ
            $errorMessages = $validator->getErrors();
            if($validator->getError('lastName')) {
                $errorMessages['name'] = $validator->getMessage('lastName');
            }elseif($validator->getError('firstName')) {
                $errorMessages['name'] = $validator->getMessage('firstName');
            }

            if($validator->getError('lastNameKana')) {
                $errorMessages['nameKana'] = $validator->getMessage('lastNameKana');
            }elseif($validator->getError('firstNameKana')) {
                $errorMessages['nameKana'] = $validator->getMessage('firstNameKana');
            }

            if($validator->getError('zipCode1')) {
                $errorMessages['zipCode'] = $validator->getMessage('zipCode1');
            }elseif($validator->getError('zipCode2')) {
                $errorMessages['zipCode'] = $validator->getMessage('zipCode2');
            }

            if($validator->getError('prefId')) {
                $errorMessages['prefId'] = $validator->getMessage('prefId');
            }

            if($validator->getError('address1')) {
                $errorMessages['address1'] = $validator->getMessage('address1');
            }

            if($validator->getError('address2')) {
                $errorMessages['address2'] = $validator->getMessage('address2');
            }

            if($validator->getError('address3')) {
                $errorMessages['address3'] = $validator->getMessage('address3');
            }

            if($validator->getError('telNo1')) {
                $errorMessages['telNo'] = $validator->getMessage('telNo1');
            }elseif($validator->getError('telNo2')) {
                $errorMessages['telNo'] = $validator->getMessage('telNo2');
            }elseif($validator->getError('telNo3')) {
                $errorMessages['telNo'] = $validator->getMessage('telNo3');
            }

            $json_data = $this->createAjaxResponse("ng", array(), $errorMessages);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function doAction() {
        //aaidにも送信
        $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());
        $shippingAddressManager->setAddress($this);

        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');
        $user_info = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);
        $this->gift_message_service->updateGreetingCardReceiverStatus($this->gift_message->id, $user_info->id);

        $json_data = $this->createAjaxResponse("ok", array(), array(), '');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
