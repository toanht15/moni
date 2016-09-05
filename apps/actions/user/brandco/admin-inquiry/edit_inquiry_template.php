<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class edit_inquiry_template extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_inquiry_template';
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $inquiry_template_category_id;
    private $inquiry_template_id;
    private $inquiry_brand;
    private $inquiry_template;
    private $inquiry_template_category;

    public function doThisFirst() {
        $this->inquiry_template_category_id = $this->GET['inquiry_template_category_id'];
        $this->inquiry_template_id =  $this->GET['inquiry_template_id'];
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("edit_inquiry_template#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("edit_inquiry_template#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'id' => $this->inquiry_template_category_id,
        ))) {
            $this->logger->error("edit_inquiry_template#validate inquiry_template_category isn't existed");

            return false;
        }

        $this->inquiry_template_category = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY);
        if ($this->inquiry_template_id) {
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE, array(
                'id' => $this->inquiry_template_id,
                'inquiry_brand_id' => $this->inquiry_brand->id
            ))
            ) {
                $this->logger->error("edit_inquiry_template#validate inquiry_template isn't existed");

                return false;
            }

            $this->inquiry_template = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE);
        }

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $inquiry_template_info['inquiry_template_category_options'] = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, array(
            'inquiry_brand_id' => $this->inquiry_brand->id
        ));

        $inquiry_template_info['inquiry_template'] = array(
            'id' => $this->inquiry_template->id,
            'inquiry_template_category_id' => $this->inquiry_template_category_id,
            'name' => $this->getValue('name', $this->inquiry_template->name ?: ''),
            'content' => $this->getValue('content', $this->inquiry_template->content ?: '')
        );

        $inquiry_template_info['prev_page'] = $this->GET['prev_page'];
        $inquiry_template_info['operator_type'] = InquiryRoom::TYPE_ADMIN;

        $this->Data['inquiry_template_info'] = $inquiry_template_info;

        return 'user/brandco/admin-inquiry/edit_inquiry_template.php';
    }

    public function getValue($key, $default = '') {
        return $this->Data['ActionError'] ? $this->Data['ActionForm'][$key] : $default;
    }
}
