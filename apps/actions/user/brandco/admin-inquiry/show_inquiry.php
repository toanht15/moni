<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class show_inquiry extends BrandcoGETActionBase {
    protected $ContainerName = 'show_inquiry';
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedRedirect = true;

    private $inquiry_room_id;
    private $inquiry_brand;
    private $inquiry_room;

    public function doThisFirst() {
        $this->inquiry_room_id = $this->GET['exts'][0];
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        if (!$this->isLoginAdmin()) {
            return 'redirect: ' . Util::rewriteUrl('my', 'login');
        }

        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("show_inquiry#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("show_inquiry#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
            'id' => $this->inquiry_room_id,
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'operator_type' => InquiryRoom::TYPE_ADMIN,
        ))
        ) {
            $this->logger->error("show_inquiry#validate inquiry_room isn't existed");

            return false;
        }

        $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);

        return true;
    }

    function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');

        $inquiry_info['operator_type'] = InquiryRoom::TYPE_ADMIN;
        $inquiry_info['inquiry_user'] = $inquiry_service->getInquiryUserDetail($this->inquiry_room->id);
        $inquiry_info['inquiry_room'] = $this->inquiry_room;
        $inquiry_info['inquiry'] = $inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $this->inquiry_room->inquiry_id));
        $inquiry_info['inquiry_messages'] = $inquiry_service->getInquiryMessages($this->inquiry_room->id);
        $inquiry_info['inquiry_message_draft'] = $inquiry_service->getInquiryMessageDraft($this->inquiry_room->id, InquiryMessage::ADMIN)[0];
        $inquiry_info['role'] = InquiryMessage::ADMIN;
        $inquiry_info['sender_list'] = $inquiry_service->getSenderList($this->getBrand(), $this->inquiry_room->id);

        $inquiry_info['inquiry_history'] = $inquiry_service->getInquiryHistory(InquiryRoom::TYPE_ADMIN, $this->inquiry_room->id, array(
            'mail_address' => $inquiry_info['inquiry_user']['mail_address'],
            'user_id' => $inquiry_info['inquiry_user']['user_id'],
        ), $this->inquiry_brand->id);

        $this->Data['inquiry_info'] = $inquiry_info;

        return 'user/brandco/admin-inquiry/show_inquiry.php';
    }
}
