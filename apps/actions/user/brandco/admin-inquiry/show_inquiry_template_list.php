<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class show_inquiry_template_list extends BrandcoGETActionBase {
    protected $ContainerName = 'show_inquiry_template_list';
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("show_inquiry_template_list#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("show_inquiry_template_list#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $this->Data['inquiry_template_info'] = $inquiry_brand_service->getInquiryTemplateList($this->inquiry_brand->id);
        $this->Data['inquiry_template_info']['operator_type'] = InquiryRoom::TYPE_ADMIN;

        return 'user/brandco/admin-inquiry/show_inquiry_template_list.php';
    }
}
