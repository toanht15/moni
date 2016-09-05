<?php

class BrandAdministratorAccountInfoServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandAdministratorAccountInfoService");
    }

    public function testBrandAdministratorAccountInfoCount() {
        $brandAdministratorAccount = $this->entity('BrandAdministratorAccountInfos',array(
            'brand_id' => 1,
            "administrator_account_no" => 1,
            "name" => "test",
            "mail_address" =>"test@mail.com",
            "tel_no1" => "090",
            "tel_no2" => "1111",
            "tel_no3" => "2222"
        ));
        $result = $this->find('BrandAdministratorAccountInfos', array('brand_id' => $brandAdministratorAccount->brand_id));
        $count_entites = $this->countEntities('BrandAdministratorAccountInfos',array($result));
        $count_account = $this->target->countRegisteredAccountListByBrandId($brandAdministratorAccount->brand_id);
        $this->assertEquals($count_entites, $count_account);
    }

    public function testBrandAdministratorAccountInfoValidate(){
        $create_name = $this->target->createValidatorForAccountName("test");
        $this->assertEquals("str", $create_name[test][type]);
        $create_email = $this->target->createValidatorForMailAddress("a@email.com");
        $this->assertEquals("MailAddress",$create_email['a@email.com']['validator'][0]);
        $tel_1 = "090";
        $tel_2 = "1111";
        $tel_3 = "2222";
        $create_tel = $this->target->validateAccountTEL($tel_1,$tel_2,$tel_3);
        $this->assertEquals("1",$create_tel);
    }

    public function testBrandAdmininstratorAccountInfoSave(){
        $account = $this->target->getEmptyAccountInfo();
        $account->brand_id = 1;
        $account->administrator_account_no = 2;
        $account->name = "test2";
        $account->mail_address = "a@mail.com";
        $account->tel_no1 = "000";
        $account->tel_no2 = "1111";
        $account->tel_no3 = "2222";
        $this->target->saveAccountInfo($account);
        $result = $this->findOne('BrandAdministratorAccountInfos', array('administrator_account_no' => 2));
        $this->assertEquals(1, $result->brand_id);
    }

    public function testBrandAdmininstratorAccountInfoEmpty(){
        $account = $this->target->getEmptyAccountInfo();
        $account->brand_id = 1;
        $account->administrator_account_no = 3;
        $this->target->saveAccountInfo($account);
        $result = $this->findOne('BrandAdministratorAccountInfos', array('administrator_account_no' => 3));
        $check_empty = $this->target->isEmptyAccountInfoParameter($result->name,$result->mail_address,$result->tel_no1,$result->tel_no2,$result->tel_no3);
        $this->assertEquals(true,$check_empty);

    }


}
