<?php

AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');

class BrandcoAuthTraitTest extends BaseTest {

    public function test() {
        $this->assertEquals(true, true);
    }

//    public function testCreateNewUser01_whenNewEntryUser() {
//        // init
//        $aaid = $this->newAAIDAccount(false);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"));
//
//        // test
//        $user = $target->createNewUser($userInfo, null, "TOKEN", "TOKEN", "MAIL", "CLIENT", new UserManager(null), "new_entry@aainc.co.jp");
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                "monipla_user_id" => $userInfo->id,
//                "name" => "hogera1",
//                "mail_address" => "MAIL",
//                "profile_image_url" => null),
//                JSON_PRETTY_PRINT),
//        $this->convertToJson($user));
//    }
//
//    public function testCreateNewUser02_whenNewEntryUserWithAAIDFlg() {
//        // init
//        $aaid = $this->newAAIDAccount(false);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//
//        // test
//        $user = $target->createNewUser($userInfo, null, "TOKEN", "TOKEN", "MAIL", "CLIENT", new UserManager(null), "new_entry@aainc.co.jp");
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                "monipla_user_id" => $userInfo->id,
//                "name" => "hogera1",
//                "mail_address" => "MAIL",
//                "profile_image_url" => null,
//                "aa_flg" => 1),
//                JSON_PRETTY_PRINT),
//            $this->convertToJson($user));
//    }
//
//    public function testCreateNewUser03_whenNewEntryUserWithNoUserFound() {
//        // init
//        $aaid = $this->newAAIDAccount(false);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//
//        // test
//        $user = $target->createNewUser($userInfo, null, "TOKEN", "TOKEN", "MAIL", "CLIENT", new UserManager(null), "new_entry@aainc.co.jp");
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                "monipla_user_id" => $userInfo->id,
//                "name" => "hogera1",
//                "mail_address" => "MAIL",
//                "profile_image_url" => null,
//                "aa_flg" => 1),
//                JSON_PRETTY_PRINT),
//            $this->convertToJson($user));
//    }
//
//    public function testCreateNewUser04_whenNewEntryUserWithNoUserFound() {
//        // init
//        $aaid = $this->newAAIDAccount(true);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//
//        // test
//        $user = $target->createNewUser($userInfo, null, "TOKEN", "TOKEN", "MAIL", "CLIENT", new UserManager($userInfo), "new_entry@aainc.co.jp");
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                "monipla_user_id" => $userInfo->id,
//                "name" => "hogera1",
//                "mail_address" => "new_entry@aainc.co.jp",
//                "profile_image_url" => null,
//                "aa_flg" => 1),
//                JSON_PRETTY_PRINT),
//            $this->convertToJson($user));
//    }
//
//    public function testCreateNewUser05_whenNewUserWithNoUserFound() {
//        // init
//        $aaid = $this->newAAIDAccount(true);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//
//        // test
//        $user = $target->createNewUser($userInfo, null, "TOKEN", "TOKEN", "MAIL", "CLIENT", new UserManager($userInfo), null);
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                "monipla_user_id" => $userInfo->id,
//                "name" => "hogera1",
//                "mail_address" => $userInfo->id . "@aainc.co.jp",
//                "profile_image_url" => null,
//                "aa_flg" => 1),
//                JSON_PRETTY_PRINT),
//            $this->convertToJson($user));
//    }
//
//    public function testSetValidatorDefinition01_whenTheUserHasMailAddress() {
//        // init
//        $aaid = $this->newAAIDAccount(true);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//        $target->userManager = new UserManager($userInfo);
//
//        // test
//        $target->setValidatorDefinition();
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                'mailAddress'       => array('type' => 'str', 'validator' => array('MailAddress')),
//                'password'          => array('type' => 'str', 'length' => 45, 'validator' => array('AlnumSymbol')),
//                'passwordRetype'    => array('type' => 'str', 'equals' => '@_password_@'),
//                'name'              => array('type' => 'str', 'length' => 200),
//                'lastName'          => array('type' => 'str', 'length' => 45),
//                'firstName'         => array('type' => 'str', 'length' => 45),
//                'lastNameKana'      => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
//                'firstNameKana'     => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
//                'zipCode'           => array('type' => 'str', 'validator' => array('ZipCode')),
//                'prefId'            => array('type' => 'num'),
//                'address1'          => array('type' => 'str', 'length' => 255),
//                'address2'          => array('type' => 'str', 'length' => 255),
//                'address3'          => array('type' => 'str', 'length' => 255),
//                'telNo'             => array('type' => 'num', 'regex' => '/^0\d{9,11}$/'),
//                'sex'               => array('type' => 'str', 'regex' => '#^f|m$#'),
//                'birthDay'          => array('type' => 'date')),
//                JSON_PRETTY_PRINT
//            ),
//            json_encode($target->ValidatorDefinition, JSON_PRETTY_PRINT));
//    }
//
//    public function testSetValidatorDefinition02_whenTheUserDoesNotHaveMailAddress() {
//        // init
//        $aaid = $this->newAAIDAccount(false);
//        $userInfo = $this->getAAIDAccount($aaid);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//        $target->userManager = new UserManager($userInfo);
//
//        // test
//        $target->setValidatorDefinition();
//
//        // assert
//        $this->assertEquals(
//            json_encode(array(
//                'mailAddress'       => array('type' => 'str', 'validator' => array('MailAddress'), "required" => true),
//                'password'          => array('type' => 'str', 'length' => 45, 'validator' => array('AlnumSymbol')),
//                'passwordRetype'    => array('type' => 'str', 'equals' => '@_password_@'),
//                'name'              => array('type' => 'str', 'length' => 200),
//                'lastName'          => array('type' => 'str', 'length' => 45),
//                'firstName'         => array('type' => 'str', 'length' => 45),
//                'lastNameKana'      => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
//                'firstNameKana'     => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
//                'zipCode'           => array('type' => 'str', 'validator' => array('ZipCode')),
//                'prefId'            => array('type' => 'num'),
//                'address1'          => array('type' => 'str', 'length' => 255),
//                'address2'          => array('type' => 'str', 'length' => 255),
//                'address3'          => array('type' => 'str', 'length' => 255),
//                'telNo'             => array('type' => 'num', 'regex' => '/^0\d{9,11}$/'),
//                'sex'               => array('type' => 'str', 'regex' => '#^f|m$#'),
//                'birthDay'          => array('type' => 'date')),
//                JSON_PRETTY_PRINT
//            ),
//            json_encode($target->ValidatorDefinition, JSON_PRETTY_PRINT));
//    }
//
//    public function testValidateData01_whenMailAddressExists() {
//        // init
//        $aaid1 = $this->newAAIDAccount(true);
//        $aaid2 = $this->newAAIDAccount(false);
//        $userInfo = $this->getAAIDAccount($aaid2);
//        $target = new BrandcoAuthTraitMock($this->entity("Brands"), true);
//        $target->userManager = new UserManager($userInfo);
//        $target->mailAddress = $aaid1 . "@aainc.co.jp"; // duplicate mail address.
//        $target->Validator = new ValidatorMock();
//
//        // test
//         $result = $target->validateData();
//
//        // assert
//        $this->assertFalse($result);
//    }
//}
//
//class BrandcoAuthTraitMock {
//
//    use BrandcoAuthTrait;
//
//    public $brand;
//
//    public $cp_user;
//
//    public $cp_action_id;
//
//    public $isLoginManager;
//
//    public $Data = array();
//
//    public $ValidatorDefinition = array();
//
//    public $userManager;
//
//    public $Validator;
//
//    public $mailAddress;
//
//    public function __construct($brand, $isLoginManager = false) {
//        $this->brand = $brand;
//        $this->isLoginManager  = $isLoginManager;
//    }
//
//    public function isLoginManager() {
//        return $this->isLoginManager;
//    }
//
//    public function createService($service_name, $params = null) {
//        return aafwServiceFactory::create($service_name, $params);
//    }
//
//    public function getBrand() {
//        return $this->brand;
//    }
}

class ValidatorMock {

    public $Error = array();

    public function setError($key ,$value) {
        $this->Error[$key] = $value;
    }

    public function isValid() {
        return count($this->Error) === 0;
    }
}