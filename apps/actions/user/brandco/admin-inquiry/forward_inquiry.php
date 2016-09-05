<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');

class forward_inquiry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'show_inquiry';
    protected $Form = array( 'package' => 'admin-inquiry', 'action' => 'show_inquiry/{inquiry_room_id}');
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    /** @var InquiryService $inquiry_service */
    private $inquiry_service;

    private $inquiry_brand;
    private $inquiry_room;

    public function doThisFirst() {
        $this->inquiry_service = $this->getService('InquiryService');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
            $this->logger->error("forward_inquiry#validate inquiry_brand isn't existed");
            $this->logger->hipchat_error("forward_inquiry#validate inquiry_brand isn't existed");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
            'id' => $this->POST['inquiry_room_id'],
            'inquiry_brand_id' => $this->inquiry_brand->id,
            'operator_type' => InquiryRoom::TYPE_ADMIN
        ))) {
            $this->logger->error("forward_inquiry#validate inquiry_room isn't existed");

            return false;
        }
        $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);

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

            $this->inquiry_service->forwardInquiry($this->inquiry_room, '【' . $this->getBrand()->enterprise_name . '様より転送されました】' . PHP_EOL . $this->POST['memo']);
            $this->sendMail();

            $inquiries->commit();
        } catch (aafwException $e) {
            $inquiries->rollback();

            $this->logger->error("forward_inquiry#doAction can't forward");
            $this->logger->error($e);
            $this->hipchat_logger->error("forward_inquiry#doAction can't forward");

            return 'redirect: ' . Util::rewriteUrl('admin-inquiry', 'show_inquiry', array($this->POST['inquiry_room_id']), array('mid' => 'notice-send-failed'));
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('admin-inquiry', 'show_inquiry', array($this->POST['inquiry_room_id']), array('mid' => 'inquiry-forward'));
    }

    /**
     *
     */
    public function sendMail() {
        $inquiry = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $this->inquiry_room->inquiry_id));

        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->getService('InquiryMailService');
        $inquiry_mail_service->send($inquiry_mail_service->getManagerToAddressList(),
            InquiryMailService::TEMPLATE_FORWARD_FROM_ADMIN, array(
                'USER_NAME' => $inquiry->user_name,
                'BRAND_NAME' => $this->getBrand()->name,
                'URL' => $inquiry_mail_service->generateUrl(InquiryRoom::TYPE_MANAGER, $this->getBrand()->directory_name,
                    array(InquiryRoom::getDir(InquiryRoom::TYPE_MANAGER), 'show_inquiry_list')),
            )
        );
    }
}
