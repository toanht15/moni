<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class api_search_inquiry extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'api_search_inquiry';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    private $page;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        $this->page = $this->GET['page'] ?: 1;
    }

    function validate() {
        return true;
    }

    function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');
        $inquiry_list = $inquiry_service->getInquiryList(InquiryRoom::TYPE_MANAGER, ($this->page - 1) * InquiryService::N_INQUIRIES_PER_PAGE, $this->GET);
        $total_count = $inquiry_service->countInquiryList(InquiryRoom::TYPE_MANAGER, $this->GET);

        $parser = new PHPParser();
        $html = Util::sanitizeOutput($parser->parseTemplate('InquiryList.php', array(
            'operator_type' => InquiryRoom::TYPE_MANAGER,
            'inquiry_list' => $inquiry_list,
            'page' => $this->page,
            'total_count' => $total_count
        )));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}

