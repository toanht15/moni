<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class show_inquiry_list extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'show_inquiry_list';
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    function validate() {
        return true;
    }

    function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $this->Data['page'] = ($this->GET['page']) ?: 1;
        $this->Data['inquiry_list'] = $inquiry_service->getInquiryList(InquiryRoom::TYPE_MANAGER, ($this->Data['page'] - 1) * InquiryService::N_INQUIRIES_PER_PAGE, array(
            'status' => array(InquiryRoom::STATUS_OPEN, InquiryRoom::STATUS_IN_PROGRESS)
        ));
        $this->Data['total_count'] = $inquiry_service->countInquiryList(InquiryRoom::TYPE_MANAGER, array(
            'status' => array(InquiryRoom::STATUS_OPEN, InquiryRoom::STATUS_IN_PROGRESS)
        ));

        // InquirySectionの取得
        $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID));
        $this->Data['inquiry_sections'] = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $inquiry_brand->id));

        return 'manager/inquiry/show_inquiry_list.php';
    }
}
