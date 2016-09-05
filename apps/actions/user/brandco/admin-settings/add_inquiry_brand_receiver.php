<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class add_inquiry_brand_receiver extends BrandcoPOSTActionBase {
    protected $ContainerName = 'inquiry_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'inquiry_settings_form',
    );
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    private $logger;
    private $hipchat_logger;
    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwlog4phplogger::getdefaultlogger();
        $this->hipchat_logger = aafwlog4phplogger::getHipChatlogger();
    }

    public function validate () {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name'  => 'mail_address',
                'type'  => InquiryValidator::VALID_MAIL_ADDRESS,
                'expected'  => 255,
                'required'  => true
            ),
        ))) {
            foreach ($inquiry_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }

            return false;
        }

        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->brand->id))) {
            $this->Validator->setError('mail_address', 'SAVE_ERROR_UNKNOWN');
            $this->logger->error("add_inquiry_brand_receiver#validate inquiry_brand (brand_id = {$this->brand->id}) is not found");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if ($inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND_RECEIVER, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'mail_address' => $this->POST['mail_address']
        ))) {
            $this->Validator->setError('mail_address', 'EXISTED_MAIL_ADDRESS');

            return false;
        }

        return true;
    }

    function doAction() {
        try {
            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand_service->createInquiryBrandReceiver($this->inquiry_brand->id, $this->POST);

        } catch (aafwException $e) {
            $this->logger->error("add_inquiry_brand_receiver#doAction can't insert into inquiry_brand_receiver");
            $this->logger->error($e);
            $this->hipchat_logger->error("add_inquiry_brand_receiver#doAction can't insert into inquiry_brand_receiver");

            return '500';
        }

        $this->Data['saved'] = 1;
        return 'redirect: '.Util::rewriteUrl('admin-settings', 'inquiry_settings_form', array(), array('mid'=>'action-registered'));
    }

}
