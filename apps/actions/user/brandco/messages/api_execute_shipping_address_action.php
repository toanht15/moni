<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

class api_execute_shipping_address_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_entry_action';
    protected $Form = array(
        'package' => 'message',
        'action' => 'thread/{cp_action_id}',
    );
    
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
            'telNo1'          => array('required' => 1, 'type' => 'num'),
            'telNo2'          => array('required' => 1, 'type' => 'num'),
            'telNo3'          => array('required' => 1, 'type' => 'num'),
            'telNo'           => array('required' => 1, 'type' => 'num', 'regex' => '/^0\d{9,11}$/'),
        );
        
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if(!$validator->isValid()) {
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        $this->POST['telNo'] = $this->POST['telNo1'] . $this->POST['telNo2'] . $this->POST['telNo3'];

        /** @var CpShippingAddressActionService $cp_shipping_address_action_service */
        $cp_shipping_address_action_service = $this->createService('CpShippingAddressActionService');
        $cp_shipping_address_action = $cp_shipping_address_action_service->getCpShippingAddressAction($this->cp_action_id);
        
        //名前不要
        if($cp_shipping_address_action->name_required != 1){
            unset($validatorDefinition['lastName']);
            unset($validatorDefinition['firstName']);
            unset($validatorDefinition['lastNameKana']);
            unset($validatorDefinition['firstNameKana']);
        }
        
        //住所不要
        if($cp_shipping_address_action->address_required != 1){
            unset($validatorDefinition['zipCode1']);
            unset($validatorDefinition['zipCode2']);
            unset($validatorDefinition['prefId']);
            unset($validatorDefinition['address1']);
            unset($validatorDefinition['address2']);
            unset($validatorDefinition['address3']);
        }
        
        //TEL不要
        if($cp_shipping_address_action->tel_required != 1){
            unset($validatorDefinition['telNo1']);
            unset($validatorDefinition['telNo2']);
            unset($validatorDefinition['telNo3']);
            unset($validatorDefinition['telNo']);
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
            }elseif($validator->getError('telNo')) {
                $errorMessages['telNo'] = $validator->getMessage('telNo');
            }

            $json_data = $this->createAjaxResponse("ng", array(), $errorMessages);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function saveData() {
        // 配送先情報保存
        /** @var ShippingAddressUserService $shipping_address_user_service */
        $shipping_address_user_service = $this->createService('ShippingAddressUserService');
        $shipping_address_user_service->setShippingAddressUser($this->cp_user_id, $this->cp_shipping_address_action_id, $this->POST);

        //aaidにも送信
        $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());
        $shippingAddressManager->setAddress($this);
    }
}
