<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class complete extends BrandcoPOSTActionBase {

    protected $ContainerName = 'inquiry';
    protected $Form = array('package' => 'inquiry', 'action' => 'index');
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );
    protected $logger;
    protected $hipchat_logger;
    public $NeedOption = array();
    public $SkipAgeAuthenticate = true;
    public $CsrfProtect = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array(
            'brand_id' => $this->getBrand()->id))
        ) {
            return false;
        }

        if ($this->POST['submit_flg'] !== '1') {
            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

        if (!$inquiry_validator->isValid($this->POST, array(
            array(
                'name' => 'operator_type',
                'type' => InquiryValidator::VALID_CHOICE,
                'expected' => array(InquiryRoom::TYPE_ADMIN, InquiryRoom::TYPE_MANAGER),
                'required' => true
            ),
            array(
                'name' => 'category',
                'type' => InquiryValidator::VALID_CHOICE,
                'expected' => Inquiry::$categories,
                'required' => true
            ),
            array(
                'name' => 'user_name',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 50,
                'required' => true
            ),
            array(
                'name' => 'mail_address',
                'type' => InquiryValidator::VALID_MAIL_ADDRESS,
                'expected' => 255,
                'required' => true
            ),
            array(
                'name' => 'content',
                'type' => InquiryValidator::VALID_TEXT,
                'expected' => 2000,
                'required' => true
            )
        ))
        ) {
            foreach ($inquiry_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }

            return false;
        }

        return true;
    }

    public function doAction() {
        $inquiries = aafwEntityStoreFactory::create('Inquiries');

        try {
            $inquiries->begin();

            // UserIDの設定 (未ログインはアノニマス)
            $user_id = $this->isLogin() ? $this->brands_users_relation->user_id : InquiryUser::USER_ID_ANONYMOUS;

            $this->createInquiry($user_id);
            $this->sendMail($user_id);

            $inquiries->commit();
        } catch (Exception $e) {
            $inquiries->rollback();
            $this->logger->error("send#doAction can't insert into inquiries");
            $this->logger->error($e);
            $this->hipchat_logger->error("send#doAction can't insert into inquiries");

            return 'redirect: ' . Util::rewriteUrl('inquiry', 'index', array(), array('mid' => 'notice-send-failed'));
        }

        if ($this->POST['referer']) {
            $this->Data['referer'] = $this->POST['referer'];
        }

        $this->Data['pageStatus']['og'] = array(
            'title' => 'お問い合わせの送信完了 - ' . $this->getBrand()->name,
        );
        $this->Data['saved'] = 1;
        $this->Data['skip_age_authentication'] = $this->isSkipAgeAuthentication();
        return 'user/brandco/inquiry/complete.php';
    }

    /**
     * @param $user_id
     */
    public function createInquiry($user_id) {
        /** @var  InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');

        $inquiry_user = $inquiry_service->getOrCreateInquiryUser(array(
            'user_id' => $user_id,
            'mail_address' => $this->POST['mail_address']
        ));

        $inquiry = $inquiry_service->createInquiry($inquiry_user->id, array(
            'user_name' => $this->POST['user_name'],
            'user_agent' => $this->SERVER['HTTP_USER_AGENT'],
            'category' => $this->POST['category'],
            'referer' => $this->POST['referer'],
            'cp_id' => $this->isNumeric($this->POST['cp_id']) ? intval($this->POST['cp_id']) : 0,
            'brand_id' => $this->getBrand()->id,
        ));

        $inquiry_room = $inquiry_service->createInquiryRoom($this->inquiry_brand->id, $inquiry->id, $this->POST['operator_type']);
        $inquiry_message = $inquiry_service->createInquiryMessage($inquiry->id, InquiryMessage::USER, array('content' => $this->POST['content'],));
        $inquiry_service->createInquiryRoomsMessagesRelation($inquiry_room->id, $inquiry_message->id);
    }

    /**
     *
     */
    public function sendMail() {
        /** @var InquiryMailService $inquiry_mail_service */
        $inquiry_mail_service = $this->getService('InquiryMailService');

        $to_addresses = array();
        if (InquiryRoom::isAdmin($this->POST['operator_type'])) {
            $to_addresses = $inquiry_mail_service->getAdminToAddressList($this->inquiry_brand);
        } else if (InquiryRoom::isManager($this->POST['operator_type'])) {
            $to_addresses = $inquiry_mail_service->getManagerToAddressList();
        }

        $inquiry_mail_service->send($to_addresses,
            InquiryMailService::TEMPLATE_OPEN, array(
                'ENTERPRISE_NAME' => InquiryRoom::isAdmin($this->POST['operator_type']) ? $this->getBrand()->enterprise_name : InquiryBrand::MANAGER_BRAND_NAME,
                'USER_NAME' => $this->POST['user_name'],
                'URL' => $inquiry_mail_service->generateUrl($this->POST['operator_type'], $this->getBrand()->directory_name, array(
                    InquiryRoom::getDir($this->POST['operator_type']), 'show_inquiry_list'
                )),
                'CONTENT' => $this->POST['content'],
                'DATE' => date("Y/m/d H:i:s")
            )
        );

    }
}
