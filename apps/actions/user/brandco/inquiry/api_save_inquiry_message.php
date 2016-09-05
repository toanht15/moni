<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_save_inquiry_message extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_save_inquiry_message';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $CsrfProtect = true;
    public $SkipAgeAuthenticate = true;

    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    private $inquiry_brand;
    private $inquiry_room;
    private $inquiry;
    private $inquiry_user;
    private $inquiry_message;
    private $inquiry_user_detail;

    public function doThisFirst() {
        $this->inquiry_service = $this->getService('InquiryService');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    function validate() {
        try {
            if (!$this->isLogin()) {
                return false;
            }

            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry_brand isn't existed");
                $this->logger->hipchat_error("api_save_inquiry_message#validate inquiry_brand isn't existed");

                return false;
            }

            $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
                'id' => $this->POST['inquiry_room_id'],
                'inquiry_brand_id' => $this->inquiry_brand->id
            ))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry_room isn't existed");

                return false;
            }

            $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY, array('id' => $this->inquiry_room->inquiry_id))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry isn't existed");

                return false;
            }

            $this->inquiry = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_USER, array('id' => $this->inquiry->inquiry_user_id))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry_user isn't existed");

                return false;
            }

            $this->inquiry_user = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_USER);
            if ($this->inquiry_user->user_id != $this->brands_users_relation->user_id) {
                $this->logger->error("api_save_inquiry_message#validate user is invalid");

                return false;
            }

            if (!$inquiry_validator->isValid($this->POST, array(
                array(
                    'name' => 'content',
                    'type' => InquiryValidator::VALID_TEXT,
                    'expected' => 2000,
                    'required' => true
                ),
                array(
                    'name' => 'draft_flg',
                    'type' => InquiryValidator::VALID_CHOICE,
                    'expected' => array(0, 1),
                    'required' => true
                ),
            ))
            ) {
                throw new APIValidationException($inquiry_validator->getErrorMessages());
            }
        } catch (APIValidationException $e) {
            $json_data = $this->createAjaxResponse("ng", array(), $e->getErrorMessage());
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    function doAction() {
        $json_data = $this->createAjaxResponse("ng", array(), array('content' => '保存した際にエラーが発生しました。時間をおいて再度お試しください。'));

        $inquiry_brands = aafwEntityStoreFactory::create('InquiryBrands');
        try {
            $inquiry_brands->begin();

            if ($this->POST['inquiry_message_id']) {
                if (!($this->inquiry_message = $this->inquiry_service->updateInquiryMessage($this->POST['inquiry_message_id'], array_merge($this->POST)))) {
                    throw new aafwException('UPDATE FAILED!');
                }
            } else {
                list($this->inquiry_message, $inquiry_rooms_messages_relation) =
                    $this->inquiry_service->createInquiryMessageAndRelation(InquiryMessage::USER, $this->inquiry->id, $this->inquiry_room->id, $this->POST);
            }

            if ($this->POST['draft_flg'] == 0) {
                $this->sendMail();
            }

            $this->inquiry_service->updateInquiryRoom($this->inquiry_room->id, array(
                'status' => InquiryRoom::STATUS_OPEN
            ));

            // 更新時間 (updated_at) を最新に
            $this->inquiry_service->updateInquiry($this->inquiry_room->inquiry_id);

            $inquiry_brands->commit();

            $parser = new PHPParser();
            $html = Util::sanitizeOutput($parser->parseTemplate('InquiryChatBody.php', array(
                'operator_type' => InquiryRoom::TYPE_ADMIN,
                'inquiry_messages' => $this->inquiry_service->getInquiryMessages($this->inquiry_room->id, true),
                'inquiry_room' => $this->inquiry_room,
                'sender_list' => $this->inquiry_service->getSenderList($this->getBrand(), $this->inquiry_room->id)
            )));

            $json_data = $this->createAjaxResponse("ok", array(
                'inquiry_message_id' => ($this->POST['draft_flg']) ? $this->inquiry_message->id : 0
            ), array(), $html);
        } catch (aafwException $e) {
            $inquiry_brands->rollback();
            $this->logger->error("api_save_inquiry_message#doAction can't insert into inquiry_messages");
            $this->logger->error($e);
            $this->hipchat_logger->error("api_save_inquiry_message#doAction can't insert into inquiry_messages");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function sendMail() {
        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->getService('InquiryMailService');

        $to_addresses = array();
        if (InquiryRoom::isAdmin($this->inquiry_room->operator_type)) {
            $to_addresses = $inquiry_mail_service->getAdminToAddressList($this->inquiry_brand);
        } else if (InquiryRoom::isManager($this->inquiry_room->operator_type)) {
            $to_addresses = $inquiry_mail_service->getManagerToAddressList();
        }

        $inquiry_mail_service->send($to_addresses,
            InquiryMailService::TEMPLATE_OPEN, array(
                'ENTERPRISE_NAME' => InquiryRoom::isAdmin($this->inquiry_room->operator_type) ? $this->getBrand()->enterprise_name : InquiryBrand::MANAGER_BRAND_NAME,
                'USER_NAME' => $this->getInquiryUserDetail()['user_name'],
                'URL' => $inquiry_mail_service->generateUrl($this->inquiry_room->operator_type, $this->getBrand()->directory_name, array(
                    InquiryRoom::getDir($this->inquiry_room->operator_type), 'show_inquiry_list'
                )),
                'CONTENT' => $this->inquiry_message->content,
                'DATE' => date("Y/m/d H:i:s")
            )
        );
    }

    /**
     * @return array
     */
    public function getInquiryUserDetail() {
        if (!$this->inquiry_user_detail) {
            $this->inquiry_user_detail = $this->inquiry_service->getInquiryUserDetail($this->inquiry_room->id);
        }

        return $this->inquiry_user_detail;
    }
}

