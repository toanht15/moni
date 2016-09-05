<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class show_inquiry extends BrandcoGETActionBase {
    protected $ContainerName = 'show';
    protected $logger;
    protected $hipchat_logger;

    public $NeedUserLogin = true;
    public $NeedRedirect = true;
    public $NeedOption = array();
    public $SkipAgeAuthenticate = true;

    private $access_token;
    private $inquiry_brand;
    private $inquiry_room;
    private $inquiry;

    public function doThisFirst() {
        $this->access_token = $this->GET['exts'][0];
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("show_inquiry#validate inquiry_brand isn't existed");
            $this->hipchat_logger->error("show_inquiry#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'access_token' => $this->access_token
        ))) {
            $this->logger->error("show_inquiry#validate inquiry_room isn't existed");

            return false;
        }

        $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY, array(
            'id' => $this->inquiry_room->inquiry_id,
        ))) {
            $this->logger->error("show_inquiry#validate inquiry isn't existed");

            return false;
        }

        $this->inquiry = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_USER, array(
            'id' => $this->inquiry->inquiry_user_id,
            'user_id' => $this->getBrandsUsersRelation()->user_id
        ))) {
            $this->logger->error("show_inquiry#validate inquiry_user isn't existed");

            return 403;
        }

        return true;
    }

    public function doAction() {
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');

        $inquiry_info['operator_type'] = InquiryRoom::TYPE_ADMIN;
        $inquiry_info['inquiry_user'] = $inquiry_service->getInquiryUserDetail($this->inquiry_room->id);
        $inquiry_info['inquiry_room'] = $this->inquiry_room;
        $inquiry_info['inquiry'] = $this->inquiry;
        $inquiry_info['inquiry_messages'] = $inquiry_service->getInquiryMessages($this->inquiry_room->id, true);
        $inquiry_info['inquiry_message_draft'] = $inquiry_service->getInquiryMessageDraft($this->inquiry_room->id, InquiryMessage::USER)[0];
        $inquiry_info['role'] = InquiryMessage::USER;
        $inquiry_info['sender_list'] = $inquiry_service->getSenderList($this->getBrand(), $this->inquiry_room->id);
        $this->Data['inquiry_info'] = $inquiry_info;
        $this->Data['pageStatus']['og'] = array(
            'title' =>'お問い合わせ - ' . $this->getBrand()->name,
        );
        $this->Data['skip_age_authentication'] = $this->isSkipAgeAuthentication();
        return 'user/brandco/inquiry/show_inquiry.php';
    }
}
