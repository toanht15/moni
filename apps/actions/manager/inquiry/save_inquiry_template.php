<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class save_inquiry_template extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'edit_inquiry_template';
    protected $Form = array(
        'package' => 'inquiry',
        'action' => 'edit_inquiry_template?inquiry_template_category_id={inquiry_template_category_id}&prev_page={prev_page}&inquiry_template_id={inquiry_template_id}'
    );
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    private $inquiry_brand;
    private $inquiry_template_category;
    private $inquiry_template;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID))) {
            $this->logger->error("save_inquiry_template#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("save_inquiry_template#validate inquiry_brand isn't existed");
            $this->Validator->setError('content', 'SAVE_UNKNOWN_ERROR');

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name' => 'name',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 50,
                'required' => true
            ),
            array(
                'name' => 'content',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 2000,
                'required' => true
            )
        ))
        ) {
            foreach ($inquiry_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }

            return false;
        }

        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY, array(
            'id' => $this->POST['inquiry_template_category_id'],
        ))
        ) {
            $this->Validator->setError('inquiry_template_category_id', 'INVALID_CHOICE');

            return false;
        }

        $this->inquiry_template_category = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY);
        if ($this->POST['inquiry_template_id']) {
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE, array(
                'inquiry_brand_id' => $this->inquiry_brand->id,
                'id' => $this->POST['inquiry_template_id']
            ))
            ) {
                $this->Validator->setError('content', 'SAVE_UNKNOWN_ERROR');

                return false;

            }

            $this->inquiry_template = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_TEMPLATE);
        }

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $order_no = $inquiry_brand_service->countRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES, array(
                'inquiry_template_category_id' => $this->inquiry_template_category->id
            )) + 1;
        if ($this->inquiry_template) {
            $inquiry_brand_service->updateInquiryTemplate($this->inquiry_template->id, array_merge($this->POST, array(
                'order_no' => $this->inquiry_template->inquiry_template_category_id != $this->POST['inquiry_template_category_id'] ? $order_no : $this->inquiry_template->order_no
            )));
        } else {
            $inquiry_brand_service->createInquiryTemplate($this->inquiry_brand->id, $this->inquiry_template_category->id, array_merge($this->POST, array(
                'order_no' => $order_no
            )));
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl(InquiryRoom::getDir(InquiryRoom::TYPE_MANAGER), $this->POST['prev_page'], array(), array(), '', true);
    }
}
