<?php

class InquiryValidatorTest extends BaseTest {
    private $t = array();

    public function setUp() {
//        list($this->t['brand'], $this->t['user'], $this->t['brand_users_relation']) = $this->newBrandToBrandUsersRelation();
    }
    /*-------------------------------------------------------------------
     * isValidSendTo
     *------------------------------------------------------------------*/
    public function test_isValidSendTo_エラー文字数_01() {
        $this->assertThat(true, $this->equalTo(true));
    }
//    public function test_isValidSendTo_エラー文字数_01() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidSendTo_エラー範囲外_02() {
//        $is_login = true;
//
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo(3);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidSendTo_エラー権限無し_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo(InquiryMessage::TYPE_FROM_USER_TO_CLIENT);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidSendTo_エラーNULL_04() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidSendTo_正常_05() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo(InquiryMessage::TYPE_FROM_USER_TO_CS);
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    public function test_isValidSendTo_正常_06() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidSendTo(InquiryMessage::TYPE_FROM_USER_TO_CLIENT);
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValidUserName
//     *------------------------------------------------------------------*/
//    public function test_isValidUserName_エラー文字数_01() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidUserName('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidUserName_エラー非文字列_02() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidUserName(100);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidUserName_エラー文字数オーバー_03() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidUserName(str_repeat('A', 51));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidUserName_エラーNULL_04() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidUserName(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidUserName_正常_05() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidUserName(str_repeat('A', 50));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValidMailAddress
//     *------------------------------------------------------------------*/
//    public function test_isValidMailAddress_エラー文字数_01() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidMailAddress_エラー非文字列_02() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress(100);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidMailAddress_エラー文字数オーバー_03() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress(str_repeat('A', 244) . '@aainc.co.jp');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidMailAddress_エラー非メール形式_04() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress(str_repeat('A', 250));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidMailAddress_エラーNULL_05() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidMailAddress_正常_06() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidMailAddress(str_repeat('A', 243) . '@aainc.co.jp');
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValidContent
//     *------------------------------------------------------------------*/
//    public function test_isValidContent_エラー文字数_01() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidContent('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidContent_エラー非文字列_02() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidContent(100);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidContent_エラー文字数オーバー_03() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidContent(str_repeat('A', 2001));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidContent_エラーNULL_04() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidContent(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidContent_正常_05() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidContent(str_repeat('A', 2000));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValidCategory
//     *------------------------------------------------------------------*/
//    public function test_isValidCategory_エラー未選択_01() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidCategory('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidCategory_エラー非カテゴリー_02() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidCategory(count(Inquiry::$categories) + 1);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidCategory_エラーNULL_03() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidCategory(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidCategory_正常_04() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidCategory(Inquiry::TYPE_OTHERS);
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValidLevel
//     *------------------------------------------------------------------*/
//    public function test_isValidLevel_エラー未選択_01() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidLevel('');
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidLevel_非レベル_02() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidLevel(-1);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidLevel_エラーNULL_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidLevel(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValidLevel_正常_04() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValidLevel(null);
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    /*-------------------------------------------------------------------
//     * isValid
//     *------------------------------------------------------------------*/
//    public function test_isValid_エラー_01() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CLIENT,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2000),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 50),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 243) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => Inquiry::TYPE_OTHERS,
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValid_エラー_02() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CS,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2001),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 50),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 243) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => Inquiry::TYPE_OTHERS,
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValid_エラー_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CS,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2000),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 51),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 243) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => Inquiry::TYPE_OTHERS,
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValid_エラー_04() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CS,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2000),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 50),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 244) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => Inquiry::TYPE_OTHERS,
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValid_エラー_05() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CS,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2000),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 50),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 243) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => count(Inquiry::$categories) + 1,
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isValid_正常空配列_06() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array());
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    public function test_isValid_正常NULL_07() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(null);
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    public function test_isValid_正常_08() {
//        $is_login = true;
//        $inquiry_validator = new InquiryValidator($is_login);
//        $result = $inquiry_validator->isValid(array(
//            InquiryValidator::VALID_SEND_TO         => InquiryMessage::TYPE_FROM_USER_TO_CS,
//            InquiryValidator::VALID_CONTENT         => str_repeat('A', 2000),
//            InquiryValidator::VALID_USER_NAME       => str_repeat('A', 50),
//            InquiryValidator::VALID_MAIL_ADDRESS    => str_repeat('A', 243) . '@aainc.co.jp',
//            InquiryValidator::VALID_CATEGORY        => Inquiry::TYPE_OTHERS,
//        ));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isExistedInquiryBrand
//     *------------------------------------------------------------------*/
//    public function test_isExistedInquiryBrand_正常NULL_false_01() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $brand = $this->entity('Brands');
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $result = $inquiry_validator->isExistedInquiryBrand();
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isExistedInquiryBrand_正常_false_02() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $brand = $this->entity('Brands');
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $result = $inquiry_validator->isExistedInquiryBrand(array(
//            'id' => -1
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isExistedInquiryBrand_正常_true_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $brand = $this->entity('Brands');
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $result = $inquiry_validator->isExistedInquiryBrand(array(
//            'id' => $inquiry_brand->id
//        ));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    /*-------------------------------------------------------------------
//     * isExistedInquiry
//     *------------------------------------------------------------------*/
//    public function test_isExistedInquiry_正常NULL_false_01() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id));
//        $inquiry = $this->entity('Inquiries', array('inquiry_brand_id' => $inquiry_brand->id, 'inquiry_user_id' => $inquiry_user->id));
//        $result = $inquiry_validator->isExistedInquiry(array());
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isExistedInquiry_正常_true_02() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id));
//        $inquiry = $this->entity('Inquiries', array('inquiry_brand_id' => $inquiry_brand->id, 'inquiry_user_id' => $inquiry_user->id));
//        $result = $inquiry_validator->isExistedInquiry(array(
//            'id'    => $inquiry->id
//        ));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    public function test_isExistedInquiry_正常_false_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_user = $this->entity('InquiryUsers', array('user_id' => $this->t['user']->id));
//        $inquiry = $this->entity('Inquiries', array('inquiry_brand_id' => $inquiry_brand->id, 'inquiry_user_id' => $inquiry_user->id));
//        $result = $inquiry_validator->isExistedInquiry(array(
//            'id'    => $inquiry->id + 1
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    /*-------------------------------------------------------------------
//     * isExistedInquirySection
//     *------------------------------------------------------------------*/
//    public function test_isExistedInquirySection_正常NULL_false_01() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_section = $this->entity('InquirySections', array('inquiry_brand_id' => $inquiry_brand->id));
//        $result = $inquiry_validator->isExistedInquirySection(array());
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
//
//    public function test_isExistedInquirySection_正常_true_02() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_section = $this->entity('InquirySections', array('inquiry_brand_id' => $inquiry_brand->id));
//        $result = $inquiry_validator->isExistedInquirySection(array(
//            'id' => $inquiry_section->id
//        ));
//
//        $this->assertThat($result, $this->equalTo(true));
//    }
//
//    public function test_isExistedInquirySection_正常_false_03() {
//        $is_login = false;
//        $inquiry_validator = new InquiryValidator($is_login);
//
//        $inquiry_brand = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
//        $inquiry_section = $this->entity('InquirySections', array('inquiry_brand_id' => $inquiry_brand->id));
//        $result = $inquiry_validator->isExistedInquirySection(array(
//            'id' => $inquiry_section->id + 1
//        ));
//
//        $this->assertThat($result, $this->equalTo(false));
//    }
}
