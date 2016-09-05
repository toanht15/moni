<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.exception.api.APIValidationException');

class api_save_inquiry_room extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_save_inquiry_room';
    protected $logger;
    protected $hipchat_logger;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $AllowContent = array('JSON');

    private $inquiry_brand;
    private $inquiry_room;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipchatLogger();
    }

    function validate() {
        try {
            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->getBrand()->id))) {
                $this->logger->error("api_save_inquiry_room#validate inquiry_brand isn't existed");
                $this->logger->hipchat_error("api_save_inquiry_room#validate inquiry_brand isn't existed");

                return false;
            }

            $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_ROOM, array(
                'id' => $this->POST['inquiry_room_id'],
                'inquiry_brand_id' => $this->inquiry_brand->id,
                'operator_type' => InquiryRoom::TYPE_ADMIN,
            ))
            ) {
                $this->logger->error("api_save_inquiry_room#validate inquiry_room isn't existed");

                return false;
            }

            // フォーム入力内容の検証
            if (!$inquiry_validator->isValid($this->POST, array(
                array(
                    'name' => 'operator_name',
                    'type' => InquiryValidator::VALID_TEXT,
                    'expected' => 50,
                    'required' => true
                ),
                array(
                    'name' => 'status',
                    'type' => InquiryValidator::VALID_CHOICE,
                    'expected' => InquiryRoom::$statuses,
                    'required' => true
                ),
                array(
                    'name' => 'inquiry_section_id_1',
                    'type' => InquiryValidator::VALID_SECTION,
                    'expected' => InquirySection::TYPE_MAJOR
                ),
                array(
                    'name' => 'inquiry_section_id_2',
                    'type' => InquiryValidator::VALID_SECTION,
                    'expected' => InquirySection::TYPE_MEDIUM
                ),
                array(
                    'name' => 'inquiry_section_id_3',
                    'type' => InquiryValidator::VALID_SECTION,
                    'expected' => InquirySection::TYPE_MINOR
                ),
                array(
                    'name' => 'remarks',
                    'type' => InquiryValidator::VALID_TEXT,
                    'expected' => 2000
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

            /** @var InquiryService $inquiry_service */
            $inquiry_service = $this->getService('InquiryService');
            $inquiry_service->updateInquiryRoom($this->inquiry_room->id, $this->POST);

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
}
