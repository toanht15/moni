<?php
AAFW::import('jp.aainc.classes.services.InquiryService');

class InquiryServiceTest extends BaseTest {
    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    private $t = array();

    public function setUp() {
        $this->inquiry_service = (new aafwServiceFactory())->create('InquiryService');
        list($this->t['brand'], $this->t['user'], $this->t['brand_users_relation']) = $this->newBrandToBrandUsersRelation();
        $this->t['inquiry_brand'] = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
        $this->t['inquiry_user'] = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => md5(uniqid()) . '@aainc.co.jp'));
    }

    public function test_getRecord_正常_01() {
        $inquiry_1 = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_2 = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $inquiry_1->id));

        $this->assertThat($inquiry_2->inquiry_user_id, $this->equalTo($inquiry_1->inquiry_user_id));
    }

    public function test_getRecord_異常_02() {
        $inquiry_1 = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_2 = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRIES, array('id' => $inquiry_1->id + 1));

        $this->assertThat($inquiry_2->inquiry_user_id, $this->isNull());
    }

    public function test_getRecords_正常_数_01() {
        $inquiry = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_message_1 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id));
        $inquiry_message_2 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id));

        $inquiry_messages = $this->inquiry_service->getRecords(InquiryService::MODEL_TYPE_INQUIRY_MESSAGES, array('inquiry_id' => $inquiry->id));

        $this->assertThat(count($inquiry_messages->toArray()), $this->equalTo(2));
    }

    public function test_getRecords_正常_値1_02() {
        $inquiry = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_message_1 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_1'));
        $inquiry_message_2 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_2'));

        $inquiry_messages = $this->inquiry_service->getRecords(InquiryService::MODEL_TYPE_INQUIRY_MESSAGES, array('inquiry_id' => $inquiry->id));
        $records = array();
        foreach ($inquiry_messages as $inquiry_message) {
            $records[] = $inquiry_message->content;
        }

        $this->assertThat($records, $this->contains('message_1'));
    }

    public function test_getRecords_異常_値2_03() {
        $inquiry = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_message_1 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_1'));
        $inquiry_message_2 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_2'));

        $inquiry_messages = $this->inquiry_service->getRecords(InquiryService::MODEL_TYPE_INQUIRY_MESSAGES, array('inquiry_id' => $inquiry->id));
        $records = array();
        foreach ($inquiry_messages as $inquiry_message) {
            $records[] = $inquiry_message->content;
        }

        $this->assertThat($records, $this->contains('message_2'));
    }

    public function test_getRecords_異常_値_04() {
        $inquiry = $this->entity('Inquiries', array('inquiry_user_id' => $this->t['inquiry_user']->id));
        $inquiry_message_1 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_1'));
        $inquiry_message_2 = $this->entity('InquiryMessages', array('inquiry_id' => $inquiry->id, 'content' => 'message_2'));

        $inquiry_messages = $this->inquiry_service->getRecords(InquiryService::MODEL_TYPE_INQUIRY_MESSAGES, array('inquiry_id' => $inquiry->id + 1));

        $this->assertThat(count($inquiry_messages), $this->equalTo(0));
    }

    /*-------------------------------------------------------------------
     * getToAddressArray_(TYPE_FROM_XX_TO_XX)_(count|value)_num
     *------------------------------------------------------------------*/
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CS_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CS, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CS_value_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CS, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CLIENT_TO_CS_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_CS, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CLIENT_TO_CS_value_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_CS, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CLIENT_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // CS + sales_manager + consultants_manager
//        $this->assertThat(count($to_address_array), $this->equalTo(3));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CLIENT_value0_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // CS
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CLIENT_value1_03() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // sales_manager
//        $this->assertThat($to_address_array, $this->contains($this->t['manager_1']->mail_address));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_USER_TO_CLIENT_value2_04() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // consultants_manager
//        $this->assertThat($to_address_array, $this->contains($this->t['manager_2']->mail_address));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_CLIENT_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // CS + sales_manager + consultants_manager
//        $this->assertThat(count($to_address_array), $this->equalTo(3));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_CLIENT_value0_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // CS
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_CLIENT_value1_03() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // sales_manager
//        $this->assertThat($to_address_array, $this->contains($this->t['manager_1']->mail_address));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_CLIENT_value2_04() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_CLIENT, $inquiry_user->id, $this->t['brand']->id);
//        // consultants_manager
//        $this->assertThat($to_address_array, $this->contains($this->t['manager_2']->mail_address));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_USER_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_USER, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CS_TO_USER_value_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CS_TO_USER, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains($inquiry_user->mail_address));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CLIENT_TO_USER_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_USER, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_TYPE_FROM_CLIENT_TO_USER_value_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_USER, $inquiry_user->id, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains($inquiry_user->mail_address));
//    }
//
//    public function test_getToAddressArray_異常_count_01() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_USER, 0, $this->t['brand']->id);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_異常_value_02() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_USER, 0, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    public function test_getToAddressArray_異常_count_03() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_USER_TO_CLIENT, $inquiry_user->id, 0);
//        $this->assertThat(count($to_address_array), $this->equalTo(1));
//    }
//
//    public function test_getToAddressArray_異常_value_04() {
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id, 'mail_address' => 'dummy_test@aainc.co.jp'));
//
//        $to_address_array = $this->inquiry_service->getToAddressArray(InquiryMessage::TYPE_FROM_CLIENT_TO_USER, 0, $this->t['brand']->id);
//        $this->assertThat($to_address_array, $this->contains(config('Mail.Support')));
//    }
//
//    /*-------------------------------------------------------------------
//     * createClauses
//     *------------------------------------------------------------------*/
//    public function test_createClauses_空配列_count_01() {
//        $clauses = $this->inquiry_service->createClauses(array());
//        $this->assertThat(count($clauses), $this->equalTo(0));
//    }
//
//    public function test_createClauses_空配列_type_02() {
//        $clauses = $this->inquiry_service->createClauses(array());
//        $this->assertThat(getType($clauses), $this->equalTo('array'));
//    }
//
//    public function test_createClauses_異常_count_01() {
//        $clauses = $this->inquiry_service->createClauses('test');
//        var_dump($clauses);
//        $this->assertThat(count($clauses), $this->equalTo(0));
//    }
//
//    public function test_createClauses_異常_type_02() {
//        $clauses = $this->inquiry_service->createClauses('test');
//        $this->assertThat(getType($clauses), $this->equalTo('array'));
//    }
//
//    public function test_createClauses_正常_count_01() {
//        $clauses = $this->inquiry_service->createClauses(array(
//            'operator_name' => 'dummy',
//        ));
//        $this->assertThat(count($clauses), $this->equalTo(1));
//    }
//
//    public function test_createClauses_正常_value_02() {
//        $clauses = $this->inquiry_service->createClauses(array(
//            'operator_name' => 'dummy',
//        ));
//        $this->assertThat($clauses, $this->contains('inquiry_meta.operator_name = "dummy"'));
//    }
}
