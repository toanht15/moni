<?php
AAFW::import('jp.aainc.classes.services.InquiryMailService');

class InquiryMailServiceTest extends BaseTest {

    private $t = array();
    /** @var  InquiryMailService $inquiry_mail_service */
    private $inquiry_mail_service;
    /** @var  InquiryBrandService $inquiry_brand_service */
    private $inquiry_brand_service;

    public function setUp() {
        $aafw_service_factory = new aafwServiceFactory();
        $this->inquiry_mail_service = $aafw_service_factory->create('InquiryMailService');
        $this->inquiry_brand_service = $aafw_service_factory->create('InquiryBrandService');

        list($this->t['brand'], $this->t['user'], $this->t['brand_users_relation']) = $this->newBrandToBrandUsersRelation();
        $this->t['manager_1'] = $this->entity('Managers', array('mail_address' => 'dummy_test_manager1@aainc.co.jp'));
        $this->t['manager_2'] = $this->entity('Managers', array('mail_address' => 'dummy_test_manager2@aainc.co.jp'));
        $this->t['sales_manager'] = $this->entity('SalesManagers', array('brand_id' => $this->t['brand']->id, 'sales_manager_id' => $this->t['manager_1']->id));
        $this->t['consultants_manager'] = $this->entity('ConsultantsManagers', array('brand_id' => $this->t['brand']->id, 'consultants_manager_id' => $this->t['manager_2']->id));
        $this->t['inquiry_brand'] = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
        $this->t['inquiry_brand_receiver_1'] = $this->entity('InquiryBrandReceivers', array('inquiry_brand_id' => $this->t['inquiry_brand']->id, 'mail_address' => 'dummy_test_manager3@aainc.co.jp'));
        $this->t['inquiry_brand_receiver_2'] = $this->entity('InquiryBrandReceivers', array('inquiry_brand_id' => $this->t['inquiry_brand']->id, 'mail_address' => 'dummy_test_manager4@aainc.co.jp'));
    }

    /**************************************************************************************************
     * generateUrl($operator_type, $brand_name = '', $params = array())
     *************************************************************************************************/

    public function test_generateUrl_TYPE_MANAGER_01() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_MANAGER);

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco_manager')));
    }

    public function test_generateUrl_TYPE_ADMIN_02() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN);

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/'));
    }

    public function test_generateUrl_TYPE_MANAGER_with_brand_name_03() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_MANAGER, 'brand_name');

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco_manager')));
    }

    public function test_generateUrl_TYPE_ADMIN_with_brand_name_04() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, 'brand_name');

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/brand_name'));
    }

    public function test_generateUrl_TYPE_MANAGER_with_brand_name_and_params_05() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_MANAGER, 'brand_name', array('test1', 'test2'));

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco_manager') . '/test1/test2'));
    }

    public function test_generateUrl_TYPE_ADMIN_with_brand_name_and_params_06() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, 'brand_name', array('test1', 'test2'));

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/brand_name/test1/test2'));
    }

    public function test_generateUrl_TYPE_ADMIN_with_brand_name_and_param_07() {
        $url = $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, 'brand_name', 'test1');

        $this->assertThat($url, $this->equalTo(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/brand_name'));
    }

    /**************************************************************************************************
     * getManagerToAddressList()
     *************************************************************************************************/

    public function test_getManagerToAddressList_count_01() {
        $addresses = $this->inquiry_mail_service->getManagerToAddressList();

        $this->assertThat(count($addresses), $this->equalTo(1));
    }

    public function test_getManagerToAddressList_value_02() {
        $addresses = $this->inquiry_mail_service->getManagerToAddressList();

        $this->assertThat($addresses, $this->contains(config('Mail.Support')));
    }

    /**************************************************************************************************
     * getSalesManagerToAddress($brand_id)
     *************************************************************************************************/

    public function test_getSalesManager_存在するbrand_id_01() {
        $manager = $this->inquiry_mail_service->getSalesManager($this->t['brand']->id);

        $this->assertThat($manager->mail_address, $this->equalTo('dummy_test_manager1@aainc.co.jp'));
    }

    public function test_getSalesManager_存在しないbrand_id_02() {
        $manager = $this->inquiry_mail_service->getSalesManager($this->t['brand']->id + 1);

        $this->assertThat($manager, $this->equalTo(null));
    }

    public function test_getSalesManager_null_03() {
        $manager = $this->inquiry_mail_service->getSalesManager();

        $this->assertThat($manager, $this->equalTo(null));
    }

    /**************************************************************************************************
     * getConsultantsManagerToAddress($brand_id)
     *************************************************************************************************/

    public function test_getConsultantsManager_存在するbrand_id_01() {
        $manager = $this->inquiry_mail_service->getConsultantsManager($this->t['brand']->id);

        $this->assertThat($manager->mail_address, $this->equalTo('dummy_test_manager2@aainc.co.jp'));
    }

    public function test_getConsultantsManager_存在しないbrand_id_02() {
        $manager = $this->inquiry_mail_service->getConsultantsManager($this->t['brand']->id + 1);

        $this->assertThat($manager, $this->equalTo(null));
    }

    public function test_getConsultantsManager_null_03() {
        $manager = $this->inquiry_mail_service->getConsultantsManager(null);

        $this->assertThat($manager, $this->equalTo(null));
    }

    /**************************************************************************************************
     * getInquiryBrandReceiverToAddress($inquiry_brand_id)
     *************************************************************************************************/

    public function test_getInquiryBrandReceiverToAddress_存在するinquiry_brand_id_count_01() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress($this->t['inquiry_brand']->id);

        $this->assertThat(count($addresses), $this->equalTo(2));
    }

    public function test_getInquiryBrandReceiverToAddress_存在するinquiry_brand_id_value_1_02() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress($this->t['inquiry_brand']->id);

        $this->assertThat($addresses, $this->contains('dummy_test_manager3@aainc.co.jp'));
    }

    public function test_getInquiryBrandReceiverToAddress_存在するinquiry_brand_id_value_2_03() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress($this->t['inquiry_brand']->id);

        $this->assertThat($addresses, $this->contains('dummy_test_manager4@aainc.co.jp'));
    }

    public function test_getInquiryBrandReceiverToAddress_存在しないinquiry_brand_id_count_04() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress($this->t['inquiry_brand']->id + 1);

        $this->assertThat(count($addresses), $this->equalTo(0));
    }

    public function test_getInquiryBrandReceiverToAddress_存在しないinquiry_brand_id_value_05() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress($this->t['inquiry_brand']->id + 1);

        $this->assertThat($addresses, $this->equalTo(null));
    }

    public function test_getInquiryBrandReceiverToAddress_null_count_06() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress();

        $this->assertThat(count($addresses), $this->equalTo(0));
    }

    public function test_getInquiryBrandReceiverToAddress_null_value_07() {
        $addresses = $this->inquiry_mail_service->getInquiryBrandReceiverToAddress();

        $this->assertThat($addresses, $this->equalTo(null));
    }
}
