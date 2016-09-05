<?php
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class InquiryBrandServiceTest extends BaseTest {

    private $t = array();
    /** @var InquiryBrandService $inquiry_brand_service */
    private $inquiry_brand_service;

    public function setUp() {
        $aafw_service_factory = new aafwServiceFactory();
        $this->inquiry_brand_service = $aafw_service_factory->create('InquiryBrandService');

        list($this->t['brand'], $this->t['user'], $this->t['brand_users_relation']) = $this->newBrandToBrandUsersRelation();
        $this->t['inquiry_brand'] = $this->entity('InquiryBrands', array('brand_id' => $this->t['brand']->id));
        $this->t['inquiry_section_1'] = $this->entity('InquirySections', array(
            'inquiry_brand_id' => $this->t['inquiry_brand']->id,
            'name' => 'section_1',
            'level' => InquirySection::TYPE_MAJOR
        ));
        $this->t['inquiry_section_2'] = $this->entity('InquirySections', array(
            'inquiry_brand_id' => $this->t['inquiry_brand']->id,
            'name' => 'section_2',
            'level' => InquirySection::TYPE_MINOR
        ));
        $this->t['inquiry_template_category'] = $this->entity('InquiryTemplateCategories', array(
            'inquiry_brand_id' => $this->t['inquiry_brand']->id,
            'name' => 'category'
        ));
        $this->t['inquiry_template'] = $this->entity('InquiryTemplates', array(
            'inquiry_brand_id' => $this->t['inquiry_brand']->id,
            'inquiry_template_category_id' => $this->t['inquiry_template_category']->id,
            'name' => 'template',
            'content' => 'content'
        ));
        $this->t['inquiry_brand_receiver'] = $this->entity('InquiryBrandReceivers', array(
            'inquiry_brand_id' => $this->t['inquiry_brand']->id,
            'mail_address' => 'dummy_test@aainc.co.jp',
        ));
    }

    /**************************************************************************************************
     * getRecord($model_type, $filter = array())
     *************************************************************************************************/

    public function test_getRecord_INQUIRY_BRANDS_存在するid_01() {
        $inquiry_brand = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('id' => $this->t['inquiry_brand']->id));

        $this->assertThat($inquiry_brand->brand_id, $this->equalTo($this->t['brand']->id));
    }

    public function test_getRecord_INQUIRY_BRANDS_存在しないid_02() {
        $inquiry_brand = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat($inquiry_brand->brand_id, $this->equalTo(null));
    }

    public function test_getRecord_INQUIRY_SECTIONS_存在するid_01() {
        $inquiry_section = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id, 'level' => InquirySection::TYPE_MAJOR));

        $this->assertThat($inquiry_section->name, $this->equalTo('section_1'));
    }

    public function test_getRecord_INQUIRY_SECTIONS_存在しないid_02() {
        $inquiry_section = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1, 'level' => InquirySection::TYPE_MAJOR));

        $this->assertThat($inquiry_section->name, $this->equalTo(null));
    }

    public function test_getRecord_INQUIRY_TEMPLATE_CATEGORIES_存在するid_01() {
        $inquiry_template_category = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $this->assertThat($inquiry_template_category->name, $this->equalTo('category'));
    }

    public function test_getRecord_INQUIRY_TEMPLATE_CATEGORIES_存在しないid_02() {
        $inquiry_template_category = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat($inquiry_template_category->name, $this->equalTo(null));
    }

    public function test_getRecord_INQUIRY_TEMPLATES_存在するid_01() {
        $inquiry_template = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $this->assertThat($inquiry_template->name, $this->equalTo('template'));
    }

    public function test_getRecord_INQUIRY_TEMPLATES_存在しないid_02() {
        $inquiry_template = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat($inquiry_template->name, $this->equalTo(null));
    }

    public function test_getRecord_INQUIRY_BRAND_RECEIVERS_存在するid_01() {
        $inquiry_brand_receiver = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $this->assertThat($inquiry_brand_receiver->mail_address, $this->equalTo('dummy_test@aainc.co.jp'));
    }

    public function test_getRecord_INQUIRY_BRAND_RECEIVERS_存在しないid_02() {
        $inquiry_brand_receiver = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat($inquiry_brand_receiver->mail_address, $this->equalTo(null));
    }

    /**************************************************************************************************
     * getRecords($model_type, $filter = array())
     *************************************************************************************************/

    public function test_getRecords_INQUIRY_SECTIONS_存在するid_count_01() {
        $inquiry_sections = $this->inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $this->assertThat(count($inquiry_sections->toArray()), $this->equalTo(2));
    }

    public function test_getRecords_INQUIRY_SECTIONS_存在するid_value_1_02() {
        $inquiry_sections = $this->inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));
        $records = array();
        foreach ($inquiry_sections as $inquiry_section) {
            $records[] = $inquiry_section->name;
        }

        $this->assertThat($records, $this->contains('section_1'));
    }

    public function test_getRecords_INQUIRY_SECTIONS_存在するid_value_2_03() {
        $inquiry_sections = $this->inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $records = array();
        foreach ($inquiry_sections as $inquiry_section) {
            $records[] = $inquiry_section->name;
        }

        $this->assertThat($records, $this->contains('section_2'));
   }

    public function test_getRecords_INQUIRY_SECTIONS_存在しないid_02() {
        $inquiry_sections = $this->inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat(count($inquiry_sections), $this->equalTo(0));
    }

    /**************************************************************************************************
     * countRecord($model_type, $filter = array())
     *************************************************************************************************/

    public function test_countRecord_INQUIRY_SECTIONS_存在するid_count_01() {
        $n_inquiry_sections = $this->inquiry_brand_service->countRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id));

        $this->assertThat($n_inquiry_sections, $this->equalTo(2));
    }

    public function test_countRecord_INQUIRY_SECTIONS_存在しないid_count_02() {
        $n_inquiry_sections = $this->inquiry_brand_service->countRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('inquiry_brand_id' => $this->t['inquiry_brand']->id + 1));

        $this->assertThat($n_inquiry_sections, $this->equalTo(0));
    }

    /**************************************************************************************************
     * deleteRecord($model_type, $id)
     *************************************************************************************************/

    public function test_deleteRecord_INQUIRY_SECTIONS_存在するid_01() {
        $this->inquiry_brand_service->deleteRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, $this->t['inquiry_section_2']->id);
        $inquiry_section = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS, array('id' => $this->t['inquiry_section_2']->id));

        $this->assertThat($inquiry_section, $this->equalTo(null));
    }

    /**************************************************************************************************
     * createInquirySection($inquiry_brand_id, $data = array())
     *************************************************************************************************/
//    public function test_createInquiryBrand01_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand = $this->inquiry_brand_service->createInquiryBrand($brand->id);
//
//        $this->assertThat($inquiry_brand->brand_id, $this->equalTo($brand->id));
//    }
//
//    public function test_updateInquiryBrand01_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand_1 = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $inquiry_brand_2 = $this->inquiry_brand_service->updateInquiryBrand($inquiry_brand_1->id);
//
//        $this->assertThat($inquiry_brand_2->id, $this->equalTo((int)$inquiry_brand_1->id));
//    }
//
//    public function test_updateInquiryBrand02_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand_1 = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $inquiry_brand_2 = $this->inquiry_brand_service->updateInquiryBrand($inquiry_brand_1->id, array(
//            'undertake_flg' => 1
//        ));
//
//        $this->assertThat($inquiry_brand_2->undertake_flg, $this->equalTo(1));
//    }
//
//    public function test_updateInquiryBrand03_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand_1 = $this->entity('InquiryBrands', array('brand_id' => $brand->id, 'undertake_flg' => 0));
//        $inquiry_brand_2 = $this->inquiry_brand_service->updateInquiryBrand($inquiry_brand_1->id);
//
//        $this->assertThat($inquiry_brand_1->undertake_flg, $this->equalTo($inquiry_brand_2->undertake_flg));
//    }
//
//    public function test_updateInquiryBrand04_異常() {
//        $inquiry_brand = $this->inquiry_brand_service->updateInquiryBrand(array('brand_id' => 0));
//
//        $this->assertThat($inquiry_brand, $this->equalTo(null));
//    }
//
//    public function test_deleteInquiryBrand01_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand_1 = $this->entity('InquiryBrands', array('brand_id' => $brand->id, 'undertake_flg' => 1));
//        $inquiry_brand_2 = $this->inquiry_brand_service->deleteInquiryBrand($inquiry_brand_1->id);
//
//        $this->assertThat($inquiry_brand_2->del_flg, $this->equalTo(null));
//    }
//
//    public function test_deleteInquiryBrand02_異常() {
//        $inquiry_brand = $this->inquiry_brand_service->deleteInquiryBrand(0);
//
//        $this->assertThat($inquiry_brand, $this->equalTo(null));
//    }
//
//    public function test_getInquiryBrand01_正常() {
//        $brand = $this->entity('Brands');
//        $inquiry_brand_1 = $this->entity('InquiryBrands', array('brand_id' => $brand->id));
//        $inquiry_brand_2 = $this->inquiry_brand_service->getInquiryBrand(array('id' => $inquiry_brand_1->id));
//
//        $this->assertThat($inquiry_brand_2->brand_id, $this->equalTo($brand->id));
//    }
//
//    public function test_getInquiryBrand02_異常() {
//        $inquiry_brand = $this->inquiry_brand_service->getInquiryBrand(array('id' => 0));
//
//        $this->assertThat($inquiry_brand->id, $this->equalTo(null));
//    }
}
