<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_save_inquiry_message extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_save_inquiry_message';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    private $inquiry_brand;
    private $inquiry_room;
    private $inquiry_user_detail;
    private $inquiry;
    private $inquiry_message;

    public function doThisFirst() {
        $this->inquiry_service = $this->getService('InquiryService');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    function validate() {
        try {
            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry_brand isn't existed");
                $this->logger->hipchat_error("api_save_inquiry_message#validate inquiry_brand isn't existed");

                return false;
            }

            $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
                'id' => $this->POST['inquiry_room_id'],
                'inquiry_brand_id' => $this->inquiry_brand->id,
                'operator_type' => InquiryRoom::TYPE_ADMIN,
            ))) {
                $this->logger->error("api_save_inquiry_message#validate inquiry_room isn't existed");

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

            $this->inquiry_room = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM);
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
                list($this->inquiry_message, $inquiry_rooms_messages_relation)
                    = $this->inquiry_service->createInquiryMessageAndRelation(InquiryMessage::ADMIN, $this->inquiry_room->inquiry_id, $this->inquiry_room->id, $this->POST);
            }

            // 下書きじゃなければ状態を変更・メールの送信
            if ($this->POST['draft_flg'] == 0) {
                if ($this->inquiry_room->status == InquiryRoom::STATUS_OPEN) {
                    $this->inquiry_service->updateInquiryRoom($this->inquiry_room->id, array('status' => InquiryRoom::STATUS_IN_PROGRESS));
                }
                $this->sendMail();
            }

            $inquiry_brands->commit();

            $parser = new PHPParser();
            $html = Util::sanitizeOutput($parser->parseTemplate('InquiryChatBody.php', array(
                'operator_type' => InquiryRoom::TYPE_ADMIN,
                'role' => InquiryMessage::ADMIN,
                'inquiry_messages' => $this->inquiry_service->getInquiryMessages($this->inquiry_room->id),
                'inquiry_room' => $this->inquiry_room,
                'sender_list' => $this->inquiry_service->getSenderList($this->getBrand(), $this->inquiry_room->id)
                )
            ));

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
        /** @var BrandContractService $brand_contract_service */
        $brand_contract_service = $this->getService('BrandContractService');
        $brand_contract = $brand_contract_service->getBrandContractByBrandId($this->getBrand()->id);

        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->getService('InquiryMailService');

        $from_address = $this->getMailFromAddress();
        if($from_address) {
            $inquiry_mail_service->setFromAddress($from_address);
        }

        $inquiry_mail_service->send($this->getInquiryUserDetail()['mail_address'],
            $this->inquiry_room->forwarded_flg ? InquiryMailService::TEMPLATE_REPLY_FROM_FORWARDED : InquiryMailService::TEMPLATE_REPLY_FROM_ADMIN,
            array(
                'SUBJECT' => $this->getBrand()->name . 'より回答がありました',
                'USER_NAME' => $this->getInquiryUserDetail()['user_name'],
                'BRAND_NAME' => $this->getBrand()->name,
                'FORWARD_FROM' => InquiryBrand::MANAGER_BRAND_NAME,
                'FORWARD_TO' => $this->getBrand()->name,
                'ACTION' => isset($this->getInquiryUserDetail()['no']) ? '内でログインを行って' : 'へアクセスして',
                'URL' => $inquiry_mail_service->generateUrl($this->inquiry_room->operator_type, $this->getBrand()->directory_name,
                    isset($this->getInquiryUserDetail()['no']) ? array('inquiry', 'show_inquiry', $this->inquiry_room->access_token) : array('inquiry')),
                'INQUIRY_URL' => $inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, $this->getBrand()->directory_name, array('inquiry')),
                'CONTENT' => $this->inquiry_message->content,
                'SIGNATURE' => in_array($brand_contract->plan, array(BrandContract::PLAN_MANAGER_STANDARD, BrandContract::PLAN_MANAGER_CP_LITE)) ? $this->getBrand()->name . '：' . Util::getBaseUrl() . PHP_EOL : '',
            )
        );
    }

    /**
     * @return mixed|true
     */
    public function getInquiry() {
        if (!$this->inquiry) {
            $this->inquiry = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array(
                'id' => $this->inquiry_room->inquiry_id
            ));
        }

        return $this->inquiry;
    }

    /**
     * @return mixed
     */
    public function getInquiryUserDetail() {
        if (!$this->inquiry_user_detail) {
            $this->inquiry_user_detail = $this->inquiry_service->getInquiryUserDetail($this->inquiry_room->id);
        }

        return $this->inquiry_user_detail;
    }
}
