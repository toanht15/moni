<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');

class show_inquiry_list extends BrandcoGETActionBase {

    protected $ContainerName = 'show_inquiry_list';
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedRedirect = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    function validate() {
        if (!$this->isLoginAdmin()) {
            return 'redirect: ' . Util::rewriteUrl ('my', 'login');
        }

        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("show_inquiry_list#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("show_inquiry_list#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

        return true;
    }

    function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');

        $this->Data['page'] = ($this->GET['page']) ?: 1;
        $this->Data['inquiry_list'] = $inquiry_service->getInquiryList(InquiryRoom::TYPE_ADMIN, ($this->Data['page'] - 1) * InquiryService::N_INQUIRIES_PER_PAGE, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'status' => array(InquiryRoom::STATUS_OPEN, InquiryRoom::STATUS_IN_PROGRESS),
        ));
        $this->Data['total_count'] = $inquiry_service->countInquiryList(InquiryRoom::TYPE_ADMIN, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'status' => array(InquiryRoom::STATUS_OPEN, InquiryRoom::STATUS_IN_PROGRESS)
        ));

        return 'user/brandco/admin-inquiry/show_inquiry_list.php';
    }
}
