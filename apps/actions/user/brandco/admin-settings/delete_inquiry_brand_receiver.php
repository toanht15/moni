<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class delete_inquiry_brand_receiver extends BrandcoPOSTActionBase {
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
    private $inquiry_brand_receiver;

    public function doThisFirst() {
        $this->logger = aafwlog4phplogger::getdefaultlogger();
        $this->hipchat_logger = aafwlog4phplogger::getHipChatlogger();
    }

    public function validate () {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->brand->id))) {
            $this->Validator->setError('mail_address', 'SAVE_ERROR_UNKNOWN');
            $this->logger->error("delete_inquiry_brand_receiver#validate inquiry_brand (brand_id = {$this->brand->id}) is not found");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND_RECEIVER, array(
            'id' => $this->POST['inquiry_brand_receiver_id'],
            'inquiry_brand_id' => $this->inquiry_brand->id,
        ))) {
            $this->Validator->setError('mail_address', 'EXISTED_MAIL_ADDRESS');

            return false;
        }

        $this->inquiry_brand_receiver = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND_RECEIVER);

        return true;
    }

    function doAction() {
        try {
            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand_service->deleteRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS, $this->POST['inquiry_brand_receiver_id']);
        } catch (aafwException $e) {
            $this->logger->error("delete_inquiry_brand_receiver#doAction can't delete inquiry_brand_receiver");
            $this->logger->error($e);
            $this->hipchat_logger->error("delete_inquiry_brand_receiver#doAction can't delete inquiry_brand_receiver");

            return '500';
        }

        $this->Data['saved'] = 1;
        return 'redirect: '.Util::rewriteUrl('admin-settings', 'inquiry_settings_form', array(), array('mid'=>'action-deleted'));
    }

}
