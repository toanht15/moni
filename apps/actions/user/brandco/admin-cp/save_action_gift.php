<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpGiftActionManager');
AAFW::import('jp.aainc.classes.services.CpGiftActionService');
AAFW::import('jp.aainc.classes.services.GiftCardConfigService');
AAFW::import('jp.aainc.classes.services.GiftCardUploadService');
AAFW::import('jp.aainc.classes.services.GiftCouponConfigService');
AAFW::import('jp.aainc.classes.services.GiftProductConfigService');

class save_action_gift extends SaveActionBase {
    protected $ContainerName = 'save_action_gift';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $file_info = array();
    private $card_upload = array();
    private $gift_coupon_config_service;
    private $gift_card_config_service;
    private $gift_card_upload_service;
    private $gift_product_config_service;
    private $expire_datetime;

    protected $ValidatorDefinition = array(
        //一般
        'title'                             => array( 'type' => 'str', 'length' => 50 ),
        'image_url'                         => array( 'type' => 'str', 'length' => 512, 'validator' => array('URL') ),
        'image_file'                        => array( 'type' => 'file', 'size' => '5MB' ),
        'text'                              => array( 'type' => 'str', 'length' => CpValidator::MAX_TEXT_LENGTH ),
        'receiver_text'                     => array( 'type' => 'str', 'length' => CpValidator::SHORT_TEXT_LENGTH ),
        'card_required'                     => array( 'type' => 'num', 'length' => 4 ),
        'incentive_type'                    => array( 'type' => 'num', 'length' => 4 ),
        //gift_card_configs
        'gift_text_color'                   => array( 'type' => 'str', 'length' => 7 ),
        'gift_to_x'                         => array( 'type' => 'num', 'length' => 3 ),
        'gift_to_y'                         => array( 'type' => 'num', 'length' => 3 ),
        'gift_to_text_size'                 => array( 'type' => 'num', 'length' => 3 ),
        'gift_to_size'                      => array( 'type' => 'num', 'length' => 3 ),

        'gift_from_x'                       => array( 'type' => 'num', 'length' => 3 ),
        'gift_from_y'                       => array( 'type' => 'num', 'length' => 3 ),
        'gift_from_text_size'               => array( 'type' => 'num', 'length' => 3 ),
        'gift_from_size'                    => array( 'type' => 'num', 'length' => 3 ),

        'gift_content_x'                    => array( 'type' => 'num', 'length' => 3 ),
        'gift_content_y'                    => array( 'type' => 'num', 'length' => 3 ),
        'gift_content_width'                => array( 'type' => 'num', 'length' => 3 ),
        'gift_content_height'               => array( 'type' => 'num', 'length' => 3 ),
        'gift_content_text_size'            => array( 'type' => 'num', 'length' => 3 ),
        'gift_content_default_text'         => array( 'type' => 'str', 'length' => 2000 ),
        //coupon
        'gift_coupon_id'                    => array( 'type' => 'num' ),
        //coupon & product
        'gift_description'                  => array( 'type' => 'str', 'length' => 2000 ),
        //product
        'gift_product_postal_name_flg'      => array( 'type' => 'num', 'length' => 4 ),
        'gift_product_postal_address_flg'   => array( 'type' => 'num', 'length' => 4 ),
        'gift_product_postal_tel_flg'       => array( 'type' => 'num', 'length' => 4 ),
        'gift_product_expire_date'          => array( 'type' => 'str', 'length' => 10 ),
        'gift_product_expire_time_hh'       => array( 'type' => 'num', 'range' => array( '<' => 24, '>=' => 0)),
        'gift_product_expire_time_mm'       => array( 'type' => 'num', 'range' => array( '<' => 60, '>=' => 0))
    );

    public function doThisFirst() {
        $this->gift_coupon_config_service       = $this->createService('GiftCouponConfigService');
        $this->gift_card_config_service         = $this->createService('GiftCardConfigService');
        $this->gift_card_upload_service         = $this->createService('GiftCardUploadService');
        $this->gift_product_config_service      = $this->createService('GiftProductConfigService');

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['incentive_type']['required'] = true;
            $this->ValidatorDefinition['receiver_text']['required'] = true;
            $this->ValidatorDefinition['gift_description']['required'] = true;

            if ($this->POST['incentive_type'] == CpGiftAction::INCENTIVE_TYPE_COUPON) {
                $this->ValidatorDefinition['gift_coupon_id']['required'] = true;
            } elseif ($this->POST['incentive_type'] == CpGiftAction::INCENTIVE_TYPE_PRODUCT) {
                $this->ValidatorDefinition['gift_product_expire_date']['required'] = true;
                $this->ValidatorDefinition['gift_product_expire_time_hh']['required'] = true;
                $this->ValidatorDefinition['gift_product_expire_time_mm']['required'] = true;
            }
        }
        if ($this->POST['card_required']) {
            $this->ValidatorDefinition['gift_text_color']['required'] = true;
            $this->ValidatorDefinition['gift_to_x']['required'] = true;
            $this->ValidatorDefinition['gift_to_y']['required'] = true;
            $this->ValidatorDefinition['gift_to_text_size']['required'] = true;
            $this->ValidatorDefinition['gift_to_size']['required'] = true;
            $this->ValidatorDefinition['gift_from_x']['required'] = true;
            $this->ValidatorDefinition['gift_from_y']['required'] = true;
            $this->ValidatorDefinition['gift_from_text_size']['required'] = true;
            $this->ValidatorDefinition['gift_from_size']['required'] = true;
            $this->ValidatorDefinition['gift_content_x']['required'] = true;
            $this->ValidatorDefinition['gift_content_y']['required'] = true;
            $this->ValidatorDefinition['gift_content_width']['required'] = true;
            $this->ValidatorDefinition['gift_content_height']['required'] = true;
            $this->ValidatorDefinition['gift_content_text_size']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if ($this->FILES['image_file']){
            $fileValidator = new FileValidator($this->FILES['image_file'],FileValidator::FILE_TYPE_IMAGE);
            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('image_file', 'NOT_MATCHES');
                return false;
            }else{
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        foreach ($this->FILES as $key=>$value) {
            if (strpos($key, 'gift_card_upload') !== false) {
                $fileValidator = new FileValidator($value, FileValidator::FILE_TYPE_IMAGE);
                if (!$fileValidator->isValidFile()) {
                    $this->Validator->setError('gift_card_upload', 'NOT_MATCHES');
                    return false;
                } else {
                    $this->card_upload[] = $fileValidator->getFileInfo();
                }
            }
        }

        if ($this->POST['card_required'] && empty($this->card_upload) && !$this->POST['gift_card_uploaded']) {
            $this->Validator->setError('gift_card_upload', 'NOT_UPLOAD_IMAGE');
            return false;
        }

        if ($this->POST['incentive_type'] != CpGiftAction::INCENTIVE_TYPE_COUPON && $this->POST['incentive_type'] != CpGiftAction::INCENTIVE_TYPE_PRODUCT) {
            return '404';
        }

        if ($this->POST['gift_coupon_id'] != '') {
            /** @var CouponService $coupon_service */
            $coupon_service = $this->createService('CouponService');
            $coupon = $coupon_service->getCouponById($this->POST['gift_coupon_id']);
            if (!$coupon || $coupon->brand_id != $brand->id) {
                return '404';
            }
        }
        if ($this->POST['incentive_type'] == CpGiftAction::INCENTIVE_TYPE_PRODUCT) {
            $this->expire_datetime = $this->POST['gift_product_expire_date'] . ' ' . $this->POST['gift_product_expire_time_hh'] . ':' . $this->POST['gift_product_expire_time_mm'] . ':00';
            if (!$validatorService->isCorrectDate($this->expire_datetime)) {
                $this->Validator->setError('gift_product_expire_datetime', 'INVALID_TIME1');
                return false;
            }
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        // ギフトアクションを保存する
        $data = array();
        if($this->FILES['image_file']){
            $data['image_url']      = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_gift");
        } else {
            $data['image_url']      = $this->POST['image_url'];
        }

        $data['title']              = $this->POST['title'];
        $data['text']               = $this->POST['text'];
        $data['receiver_text']      = $this->POST['receiver_text'];
        $data['card_required']      = $this->POST['card_required'] ? : 0;
        $data['incentive_type']     = $this->POST['incentive_type'];

        $this->getCpAction()->status     = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        // クーポン情報を保存する
        if ($this->POST['incentive_type'] == CpGiftAction::INCENTIVE_TYPE_COUPON && $this->POST['gift_coupon_id']) {
            $data_coupon_config = array();
            $data_coupon_config['cp_gift_action_id']    = $this->getConcreteAction()->id;
            $data_coupon_config['coupon_id']            = $this->POST['gift_coupon_id'];
            $data_coupon_config['message']              = $this->POST['gift_description'];
            $this->gift_coupon_config_service->setGiftCouponConfig($data_coupon_config);
        }

        // 商品情報を保存する
        if ($this->POST['incentive_type'] == CpGiftAction::INCENTIVE_TYPE_PRODUCT) {
            $data_product_config = array();
            $data_product_config['cp_gift_action_id']       = $this->getConcreteAction()->id;
            $data_product_config['product_text']            = $this->POST['gift_description'];
            $data_product_config['postal_name_flg']         = $this->POST['gift_product_postal_name_flg'] ? : 0;
            $data_product_config['postal_address_flg']      = $this->POST['gift_product_postal_address_flg'] ? : 0;
            $data_product_config['postal_tel_flg']          = $this->POST['gift_product_postal_tel_flg'] ? : 0;
            $data_product_config['expire_datetime']         = $this->expire_datetime;
            $this->gift_product_config_service->setGiftProductConfig($data_product_config);
        }

        // グリーティングカードの設定情報を保存する
        if ($this->POST['card_required']) {
            $data_card_config = array();
            $data_card_config['cp_gift_action_id']              = $this->getConcreteAction()->id;

            $data_card_config['text_color']                     = $this->POST['gift_text_color'];
            $data_card_config['from_x']                         = $this->POST['gift_from_x'];
            $data_card_config['from_y']                         = $this->POST['gift_from_y'];
            $data_card_config['from_text_size']                 = $this->POST['gift_from_text_size'];
            $data_card_config['from_size']                      = $this->POST['gift_from_size'];

            $data_card_config['to_x']                           = $this->POST['gift_to_x'];
            $data_card_config['to_y']                           = $this->POST['gift_to_y'];
            $data_card_config['to_text_size']                   = $this->POST['gift_to_text_size'];
            $data_card_config['to_size']                        = $this->POST['gift_to_size'];

            $data_card_config['content_x']                      = $this->POST['gift_content_x'];
            $data_card_config['content_y']                      = $this->POST['gift_content_y'];
            $data_card_config['content_width']                  = $this->POST['gift_content_width'];
            $data_card_config['content_height']                 = $this->POST['gift_content_height'];
            $data_card_config['content_text_size']              = $this->POST['gift_content_text_size'];
            $data_card_config['content_default_text']           = $this->POST['gift_content_default_text'];

            $gift_card_config = $this->gift_card_config_service->updateGiftCardConfig($data_card_config);

            // アップロード画像を対処する
            if ($gift_card_config) {
                // 必要ない画像を削除する
                $gift_card_upload = $this->gift_card_upload_service->getGiftCardUploads($gift_card_config->id);
                foreach ($gift_card_upload as $element) {
                    if (!in_array($element->id, $this->POST['gift_card_uploaded'])) {
                        $this->gift_card_upload_service->deleteGiftCardUpload($element->id);
                    }
                }

                // 新しい画像をアップロードする
                foreach ($this->card_upload as $element) {
                    $image_url = StorageClient::getInstance()->putObject(
                        StorageClient::toHash('brand/'.$this->Data['brand']->id . '/gift_card_upload/' . StorageClient::getUniqueId()), $element
                    );
                    $this->gift_card_upload_service->createGiftCardUpload($gift_card_config->id, $image_url);
                }
            }
        }

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpGiftActionManager');
    }
}
