<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_save_inquiry_room extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'api_save_inquiry_room';
    protected $AllowContent = array('JSON');
    protected $logger;
    protected $hipchat_logger;

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    private $inquiry_room;

    public function doThisFirst() {
        $this->inquiry_service = $this->getService('InquiryService');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipchatLogger();
    }

    function validate() {
        try {
            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
                'id' => $this->POST['inquiry_room_id'],
                'operator_type' => InquiryRoom::TYPE_MANAGER
            ))) {
                $this->logger->error("api_save_inquiry_room#validate inquiry_room isn't existed");

                return false;
            }

            if (!$inquiry_validator->isValid($this->POST, array(
                array(
                    'name'  => 'operator_name',
                    'type'  => InquiryValidator::VALID_TEXT,
                    'expected'  => 50,
                    'required' => true,
                ),
                array(
                    'name'  => 'status',
                    'type'  => InquiryValidator::VALID_CHOICE,
                    'expected'  => InquiryRoom::$statuses,
                    'required'  => true
                ),
                array(
                    'name'  => 'inquiry_section_id_1',
                    'type'  => InquiryValidator::VALID_SECTION,
                    'expected'  => InquirySection::TYPE_MAJOR
                ),
                array(
                    'name'  => 'inquiry_section_id_2',
                    'type'  => InquiryValidator::VALID_SECTION,
                    'expected'  => InquirySection::TYPE_MEDIUM
                ),
                array(
                    'name'  => 'inquiry_section_id_3',
                    'type'  => InquiryValidator::VALID_SECTION,
                    'expected'  => InquirySection::TYPE_MINOR
                ),
                array(
                    'name'  => 'remarks',
                    'type'  => InquiryValidator::VALID_TEXT,
                    'expected'  => 2000
                )
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
        $json_data = $this->createAjaxResponse("ng", array(), array('operator_name' => '保存した際にエラーが発生しました。時間をおいて再度お試しください。'));

        $inquiry_brands = aafwEntityStoreFactory::create('InquiryBrands');
        try {
            $inquiry_brands->begin();

            $this->inquiry_service->updateInquiryRoom($this->inquiry_room->id, $this->POST);

            // クライアントから転送されてきたお問い合わせの場合、ステータスが完了時に通知メールを送る
            if ($this->POST['status'] == InquiryRoom::STATUS_CLOSED
                && $this->POST['status'] != $this->inquiry_room->status
                && $this->inquiry_room->forwarded_flg) {

                $this->sendMail();
            }

            $inquiry_brands->commit();

            $json_data = $this->createAjaxResponse("ok");
        } catch (aafwException $e) {
            $inquiry_brands->rollback();
            $this->logger->error("api_save_inquiry_room#doAction can't update inquiry_info");
            $this->logger->error($e);
            $this->hipchat_logger->error("api_save_inquiry_room#doAction can't update inquiry_info");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
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
            InquiryMailService::TEMPLATE_ALERT_CLOSED,
            array(
                'USER_NAME' => $inquiry->user_name,
                'ENTERPRISE_NAME' => $brand->enterprise_name,
            )
        );
    }
}
