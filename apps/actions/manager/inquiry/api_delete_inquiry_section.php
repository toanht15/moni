<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_delete_inquiry_section extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'api_delete_inquiry_section';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

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

            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_SECTION, array(
                'id' => $this->POST['inquiry_section_id'],
                'inquiry_brand_id' => $this->inquiry_brand->id,
            ))
            ) {
                throw new APIValidationException(array('name' => $inquiry_validator->getErrorMessage('NOT_CHOOSE')));
            }
        } catch (APIValidationException $e) {
            $json_data = $this->createAjaxResponse("ng", array(), $e->getErrorMessage());
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    function doAction() {
        $json_data = $this->createAjaxResponse("ng", array(), array('name' => '保存した際にエラーが発生しました。時間をおいて再度お試しください。'));

        try {
            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand_service->deleteRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, $this->POST['inquiry_section_id']);

            $inquiry_sections = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->inquiry_brand->id));
            $parser = new PHPParser();
            $html = Util::sanitizeOutput($parser->parseTemplate('InquirySection.php', array(
                'inquiry_sections' => $inquiry_sections,
            )));

            $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        } catch (aafwException $e) {
            $this->logger->error("api_delete_inquiry_section#doAction can't insert into inquiry_sections");
            $this->logger->error($e);
            $this->hipchat_logger->error("api_delete_inquiry_section#doAction can't insert into inquiry_sections");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
