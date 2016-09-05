<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class forward_inquiry extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'show_inquiry';
    protected $Form = array('package' => 'inquiry', 'action' => 'show_inquiry/{inquiry_room_id}');
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    private $inquiry_room;
    private $inquiry_message;
    private $inquiry_rooms_messages_relation;

    public function doThisFirst() {
        $this->inquiry_service = $this->getService('InquiryService');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
            'id' => $this->POST['inquiry_room_id'],
            'operator_type' => InquiryRoom::TYPE_MANAGER
        ))
        ) {
            $this->logger->error("api_save_inquiry_message#validate inquiry_room isn't existed");

            return false;
        }

        $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_MESSAGE, array(
            'id' => $this->POST['inquiry_message_id'],
            'inquiry_id' => $this->inquiry_room->inquiry_id
        ))
        ) {
            $this->logger->error("api_save_inquiry_message#validate inquiry_message isn't existed");
            $this->Validator->setError('inquiry_message_id', 'NOT_CHOOSE_MESSAGE');

            return false;
        }

        $this->inquiry_message = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_MESSAGE);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOMS_MESSAGES_RELATION, array(
            'inquiry_room_id' => $this->inquiry_room->id,
            'inquiry_message_id' => $this->inquiry_message->id,
        ))
        ) {
            $this->logger->error("api_save_inquiry_message#validate inquiry_rooms_messages_relation isn't existed");
            $this->Validator->setError('inquiry_message_id', 'NOT_CHOOSE_MESSAGE');

            return false;
        }

        $this->inquiry_rooms_messages_relation = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOMS_MESSAGES_RELATION);

        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name' => 'memo',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 2000
            )
        ))
        ) {
            foreach ($inquiry_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }
        }

        return !$this->Validator->getErrorCount();
    }

    function doAction() {
        $inquiries = aafwEntityStoreFactory::create('Inquiries');

        try {
            $inquiries->begin();

            $this->inquiry_service->forwardInquiry($this->inquiry_room,'【' . InquiryBrand::MANAGER_BRAND_NAME . 'より転送されました】' . PHP_EOL . $this->POST['memo'], $this->inquiry_rooms_messages_relation->id);
            $this->sendMail();

            $inquiries->commit();
        } catch (aafwException $e) {
            $inquiries->rollback();

            $this->logger->error("forward_inquiry#doAction can't forward");
            $this->logger->error($e);
            $this->hipchat_logger->error("forward_inquiry#doAction can't forward");

            return 'redirect: ' . Util::rewriteUrl('admin-inquiry', 'show_inquiry', array($this->POST['inquiry_room_id']), array('mid' => 'notice-send-failed'), '', true);
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('inquiry', 'show_inquiry', array($this->POST['inquiry_room_id']), array('mid' => 'inquiry-forward'), '', true);
    }

    public function sendMail() {
        $inquiry = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $this->inquiry_room->inquiry_id));

        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');
        $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('id' => $this->inquiry_room->inquiry_brand_id));

        /** @var BrandService $brand_service */
        $brand_service = $this->getService('BrandService');
        $brand = $brand_service->getBrandById($inquiry_brand->brand_id);

        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->getService('InquiryMailService');
        $inquiry_mail_service->send($inquiry_mail_service->getAdminToAddressList($inquiry_brand),
            InquiryMailService::TEMPLATE_FORWARD_FROM_MANAGER, array(
                'USER_NAME' => $inquiry->user_name,
                'ENTERPRISE_NAME' => $brand->name,
                'URL' => $inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, $brand->directory_name,
                    array(InquiryRoom::getDir(InquiryRoom::TYPE_ADMIN), 'show_inquiry_list')),
                'DATE' => Util::getFormatDateTimeString($this->inquiry_message->created_at),
                'CONTENT' => $this->inquiry_message->content,
            )
        );
    }
}