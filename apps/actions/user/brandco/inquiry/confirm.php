<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class confirm extends BrandcoPOSTActionBase {

    protected $ContainerName = 'inquiry';
    protected $Form = array('package' => 'inquiry', 'action' => 'index');
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );
    protected $logger;
    protected $hipchat_logger;
    public $NeedOption = array();
    public $SkipAgeAuthenticate = true;
    public $CsrfProtect = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array(
            'brand_id' => $this->getBrand()->id))
        ) {
            $this->logger->error("confirm#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("confirm#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache('inquiry_brand');

        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name' => 'operator_type',
                'type' => InquiryValidator::VALID_CHOICE,
                'expected' => array(InquiryRoom::TYPE_ADMIN, InquiryRoom::TYPE_MANAGER),
                'required' => true
            ),
            array(
                'name' => 'category',
                'type' => InquiryValidator::VALID_CHOICE,
                'expected' => Inquiry::$categories,
                'required' => true
            ),
            array(
                'name' => 'user_name',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 50,
                'required' => true
            ),
            array(
                'name' => 'mail_address',
                'type' => InquiryValidator::VALID_MAIL_ADDRESS,
                'expected' => 255,
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

        return true;
    }

    function doAction() {
        $this->Data['pageStatus']['og'] = array(
            'title' => 'お問い合わせ内容の確認 - ' . $this->getBrand()->name,
        );

        $this->Data['inquiry'] = $this->POST;
        $this->Data['saved'] = 1;
        $this->Data['skip_age_authentication'] = $this->isSkipAgeAuthentication();
        return 'user/brandco/inquiry/confirm.php';
    }
}