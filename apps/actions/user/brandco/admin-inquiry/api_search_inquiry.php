<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_search_inquiry extends BrandcoGETActionBase {

    protected $ContainerName = 'api_search_inquiry';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $page;
    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        $this->page = $this->GET['page'] ?: 1;
    }

    function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("api_search_inquiry#validate inquiry_brand isn't existed");
            $this->logger->hipchat_error("api_search_inquiry#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

        return true;
    }

    function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');
        $inquiry_list = $inquiry_service->getInquiryList(InquiryRoom::TYPE_ADMIN, ($this->page - 1) * InquiryService::N_INQUIRIES_PER_PAGE, array_merge($this->GET, array(
            'inquiry_brand_id' => $this->inquiry_brand->id
        )));

        $parser = new PHPParser();
        $html = Util::sanitizeOutput($parser->parseTemplate('InquiryList.php', array(
            'operator_type' => InquiryRoom::TYPE_ADMIN,
            'inquiry_list' => $inquiry_list,
            'page' => $this->page,
            'total_count' => $this->GET['total_count']
        )));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
