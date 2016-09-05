<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class save_inquiry_template_category extends BrandcoPOSTActionBase {
    protected $ContainerName = 'show_inquiry_template_list';
    protected $Form = array( 'package' => 'admin-inquiry', 'action' => 'show_inquiry_template_list');
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("save_inquiry_template_category#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("save_inquiry_template_category#validate inquiry_brand isn't existed");
            $this->Validator->setError('name', 'SAVE_UNKNOWN_ERROR');

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if ($inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'name' => $this->POST['name']
        ))) {
            $this->Validator->setError('name', 'EXISTED_CATEGORY');

            return false;
        }

        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name'  => 'name',
                'type'  => InquiryValidator::VALID_TEXT,
                'expected'  => 50,
                'required'  => true
            ),
        ))
        ) {
            foreach ($inquiry_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }

            return false;
        }

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $inquiry_brand_service->createInquiryTemplateCategory($this->inquiry_brand->id, array(
            'name'      => $this->POST['name'],
            'order_no'  => $inquiry_brand_service->countRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, array('inquiry_brand_id' => $this->inquiry_brand->id)) + 1
        ));

        return 'redirect: ' . Util::rewriteUrl(InquiryRoom::getDir(InquiryRoom::TYPE_ADMIN), 'show_inquiry_template_list');
    }
}
