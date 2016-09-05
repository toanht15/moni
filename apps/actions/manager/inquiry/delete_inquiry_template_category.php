<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class delete_inquiry_template_category extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'show_inquiry_template_list';
    protected $Form = array( 'package' => 'inquiry', 'action' => 'show_inquiry_template_list');
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    private $inquiry_brand;
    private $inquiry_template_category;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID))) {
            $this->logger->error("delete_inquiry_template_category#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("delete_inquiry_template_category#validate inquiry_brand isn't existed");
            $this->Validator->setError('inquiry_template_category_id', 'SAVE_UNKNOWN_ERROR');

            return false;
        }

       $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'id' => $this->POST['inquiry_template_category_id']
        ))) {
            $this->Validator->setError('inquiry_template_category_id', 'INVALID_CHOICE');

            return false;
        }

        $this->inquiry_template_category = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY);

        return true;
    }

    function doAction() {
        $inquiry_brands = aafwEntityStoreFactory::create('InquiryBrands');
        try {
            $inquiry_brands->begin();

            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_templates = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES, array(
                'inquiry_template_category_id' => $this->inquiry_template_category->id
            ));

            foreach ($inquiry_templates as $inquiry_template) {
                $inquiry_brand_service->deleteRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES, $inquiry_template->id);
            }
            $inquiry_brand_service->deleteRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, $this->inquiry_template_category->id);
            $inquiry_brand_service->refreshInquiryTemplateCategoryOrderNo($this->inquiry_brand->id);

            $inquiry_brands->commit();
        } catch (aafwException $e) {
            $inquiry_brands->rollback();
            $this->logger->error("delete_inquiry_template_category#doAction can't delete inquiry_template_category");
            $this->logger->error($e);
            $this->hipchat_logger->error("delete_inquiry_template_category#doAction can't delete inquiry_template_category");
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl(InquiryRoom::getDir(InquiryRoom::TYPE_MANAGER), 'show_inquiry_template_list', array(), array(), '', true);
    }
}
