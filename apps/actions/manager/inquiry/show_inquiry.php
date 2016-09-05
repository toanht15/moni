<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class show_inquiry extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'show_inquiry';
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;

    private $inquiry_room_id;
    private $inquiry_room;

    public function doThisFirst() {
        $this->inquiry_room_id = $this->GET['exts'][0];
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();

        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
            'id' => $this->inquiry_room_id,
            'operator_type' => InquiryRoom::TYPE_MANAGER,
        ))) {
            $this->logger->error("show_inquiry#validate inquiry_room isn't existed");

            return false;
        }

        $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);

        return true;
    }

    function doAction() {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');
        /** @var BrandService $brand_service */
        $brand_service = $this->getService('BrandService');

        $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => InquiryBrand::MANAGER_BRAND_ID));

        $inquiry_info['operator_type'] = InquiryRoom::TYPE_MANAGER;
        $inquiry_info['inquiry_user'] = $inquiry_service->getInquiryUserDetail($this->inquiry_room->id);
        $inquiry_info['inquiry_room'] = $this->inquiry_room;
        $inquiry_info['inquiry'] = $inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $this->inquiry_room->inquiry_id));
        $inquiry_info['inquiry_sections'] = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $inquiry_brand->id));
        $inquiry_info['inquiry_messages'] = $inquiry_service->getInquiryMessages($this->inquiry_room->id);
        $inquiry_info['inquiry_message_draft'] = $inquiry_service->getInquiryMessageDraft($this->inquiry_room->id, InquiryMessage::MANAGER)[0];
        $inquiry_info['role'] = InquiryMessage::MANAGER;

        $brand = $brand_service->getBrandById($inquiry_info['inquiry']->brand_id);
        $inquiry_info['sender_list'] = $inquiry_service->getSenderList($brand, $this->inquiry_room->id);
        $inquiry_info['inquiry_history'] = $inquiry_service->getInquiryHistory(InquiryRoom::TYPE_MANAGER, $this->inquiry_room->id, array(
            'mail_address'      => $inquiry_info['inquiry_user']['mail_address'],
            'user_id'           => $inquiry_info['inquiry_user']['user_id'],
        ));

        $this->Data['inquiry_info'] = $inquiry_info;

        return 'manager/inquiry/show_inquiry.php';
    }
}
