<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_add_inquiry_section extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'api_add_inquiry_section';

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    function validate() {
        try {
            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID))) {
                $this->logger->error("show_inquiry#validate inquiry_brand isn't existed");
                $this->hipchat_logger->error("show_inquiry#validate inquiry_brand isn't existed");

                return false;
            }

            $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

            if (!$inquiry_validator->isValid($this->POST, array(
                array(
                    'name' => 'level',
                    'type' => InquiryValidator::VALID_CHOICE,
                    'expected' => InquirySection::$levels,
                    'required' => true
                ),
                array(
                    'name' => 'name',
                    'type' => InquiryValidator::VALID_TEXT,
                    'expected' => 50,
                    'required' => true
                ),
            ))
            ) {
                $this->logger->error("error1");
                throw new APIValidationException($inquiry_validator->getErrorMessages());
            } else if ($inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_SECTION, array(
                'inquiry_brand_id' => $this->inquiry_brand->id,
                'level' => $this->POST['level'],
                'name' => $this->POST['name']
            ))
            ) {
                $this->logger->error("error2");
                throw new APIValidationException(array('name' => $inquiry_validator->getErrorMessage('EXISTED_SECTION')));
            }

        } catch (APIValidationException $e) {
            $this->logger->error("error3");
            $json_data = $this->createAjaxResponse("ng", array(), $e->getErrorMessage());
            $this->assign('json_data', $json_data);

            return false;
        }
        $this->logger->error("error4");

        return true;
    }

    function doAction() {
        $json_data = $this->createAjaxResponse("ng", array(), array('name' => '保存した際にエラーが発生しました。時間をおいて再度お試しください。'));

        try {
            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand_service->createInquirySection($this->inquiry_brand->id, $this->POST);

            // セクション部分を更新
            $inquiry_sections = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->inquiry_brand->id));
            $parser = new PHPParser();
            $html = Util::sanitizeOutput($parser->parseTemplate('InquirySection.php', array(
                'inquiry_sections' => $inquiry_sections,
            )));

            $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        } catch (aafwException $e) {
            $this->logger->error("api_add_inquiry_section#doAction can't insert into inquiry_sections");
            $this->logger->error($e);
            $this->hipchat_logger->error("api_add_inquiry_section#doAction can't insert into inquiry_sections");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
