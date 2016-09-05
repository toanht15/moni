<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class show_inquiry_template extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'show_inquiry_template';
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;

    private $inquiry_template_id;
    private $inquiry_template;
    private $inquiry_brand;

    public function doThisFirst() {
        $this->inquiry_template_id = $this->GET['inquiry_template_id'] ?: 0;
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID))) {
            $this->logger->error("show_inquiry_template#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("show_inquiry_template#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE, array('id' => $this->inquiry_template_id))) {
            $this->inquiry_template = array(
                'id' => 0,
                'inquiry_template_category_id' => 0,
                'content' => ''
            );
        } else {
            $this->inquiry_template = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE)->toArray();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY, array('id' => $this->inquiry_template['inquiry_template_category_id']))) {
                $this->logger->error("show_inquiry_template#validate inquiry_category isn't existed");
                $this->hipchat_logger->error("show_inquiry_template#validate inquiry_category isn't existed");

                return false;
            }
        }

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $inquiry_template_category_options = $inquiry_brand_service->getInquiryTemplateCategories($this->inquiry_brand->id);
        $inquiry_template_options = array();
        foreach ($inquiry_template_category_options as $inquiry_template_category_option) {
            $records = $inquiry_brand_service->getInquiryTemplates($inquiry_template_category_option['id']);
            foreach ($records as $record) {
                $inquiry_template_options[] = $record;
            }
        }

        $inquiry_template_info['inquiry_template_category_options'] = $inquiry_template_category_options;
        $inquiry_template_info['inquiry_template_options'] = $inquiry_template_options;
        $inquiry_template_info['inquiry_template'] = $this->inquiry_template;
        $inquiry_template_info['operator_type'] = InquiryRoom::TYPE_MANAGER;

        $this->Data['inquiry_template_info'] = $inquiry_template_info;

        return 'manager/inquiry/show_inquiry_template.php';
    }
}

