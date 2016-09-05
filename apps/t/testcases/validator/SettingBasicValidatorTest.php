<?php
AAFW::import('jp.aainc.classes.validator.SettingBasicValidator');

class SettingBasicValidatorTest extends BaseTest {
    private $brand;

    protected function setUp() {
        $this->brand = $this->entity('Brands');
    }

    public function test_isManager_01() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array());
        $this->assertThat($setting_basic_validator->isManager(), $this->equalTo(false));
    }

    public function test_isManager_02() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array(), true);
        $this->assertThat($setting_basic_validator->isManager(), $this->equalTo(true));
    }

    public function test_isNotPermanent_01() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array());
        $this->assertThat($setting_basic_validator->isNotPermanent(), $this->equalTo(true));
    }

    public function test_isNotPermanent_02() {
        $setting_basic_validator = new SettingBasicValidator(array('permanent_flg' => true), array(), array());
        $this->assertThat($setting_basic_validator->isNotPermanent(), $this->equalTo(false));
    }

    public function test_isNotShippingMethodPresent_01() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array());
        $this->assertThat($setting_basic_validator->isNotShippingMethodPresent(), $this->equalTo(true));
    }

    public function test_isNotShippingMethodPresent_02() {
        $setting_basic_validator = new SettingBasicValidator(array('shipping_method' => Cp::SHIPPING_METHOD_PRESENT), array(), array());
        $this->assertThat($setting_basic_validator->isNotShippingMethodPresent(), $this->equalTo(false));
    }

    public function test_isValid_01() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array());
        $this->assertThat($setting_basic_validator->isValid(), $this->equalTo(true));
    }

    public function test_isValid_02() {
        $setting_basic_validator = new SettingBasicValidator(array(), array(), array());
        $setting_basic_validator->setError('dummy', 'dummy');
        $this->assertThat($setting_basic_validator->isValid(), $this->equalTo(false));
    }

    public function test_canFix_01() {
        $cp = $this->entity('Cps', array('brand_id' => $this->brand->id, 'fix_basic_flg' => Cp::SETTING_FIX, 'status' => Cp::STATUS_FIX));
        $setting_basic_validator = new SettingBasicValidator(array('cp_id' => $cp->id), array(), array());
        $this->assertThat($setting_basic_validator->canFix(), $this->equalTo(false));
    }

    public function test_canFix_02() {
        $cp = $this->entity('Cps', array('brand_id' => $this->brand->id, 'fix_basic_flg' => Cp::SETTING_DRAFT, 'status' => Cp::STATUS_FIX));
        $setting_basic_validator = new SettingBasicValidator(array('cp_id' => $cp->id), array(), array());
        $this->assertThat($setting_basic_validator->canFix(), $this->equalTo(true));
    }

    public function test_canFix_03() {
        $cp = $this->entity('Cps', array('brand_id' => $this->brand->id, 'fix_basic_flg' => Cp::SETTING_FIX, 'status' => Cp::STATUS_DEMO));
        $setting_basic_validator = new SettingBasicValidator(array('cp_id' => $cp->id), array(), array());
        $this->assertThat($setting_basic_validator->canFix(), $this->equalTo(true));
    }

    public function test_canSave_01() {
        $cp = $this->entity('Cps', array('brand_id' => $this->brand->id, 'fix_basic_flg' => Cp::SETTING_DRAFT));
        $setting_basic_validator = new SettingBasicValidator(array('cp_id' => $cp->id, 'save_type' => Cp::SETTING_FIX), array(), array());
        $this->assertThat($setting_basic_validator->canSave(), $this->equalTo(true));
    }

    public function test_canSave_02() {
        $cp = $this->entity('Cps', array('brand_id' => $this->brand->id, 'fix_basic_flg' => Cp::SETTING_DRAFT));
        $setting_basic_validator = new SettingBasicValidator(array('cp_id' => $cp->id, 'save_type' => Cp::SETTING_FIX), array(), array());
        $setting_basic_validator->setError('dummy', 'dummy');
        $this->assertThat($setting_basic_validator->canSave(), $this->equalTo(false));
    }

    public function test_setRequired_01() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
        ), array(), array());

        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['title']['required'], $this->equalTo(false));
    }

    public function test_setRequired_02() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
        ), array(), array());

        $setting_basic_validator->setRequired('title', true);
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['title']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_01() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_DRAFT
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['winner_count']['required'], $this->equalTo(false));
    }

    public function test_checkAndSetRequired_02() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'show_recruitment_note' => Cp::FLAG_SHOW_VALUE,
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['recruitment_note']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_03() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'use_cp_page_close_flg' => Cp::FLAG_SHOW_VALUE,
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['cp_page_close_date']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_04() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'selection_method' => CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
        ), array(), array(), true);

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['salesforce_id']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_05() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'fix_basic_flg' => Cp::SETTING_DRAFT,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['title']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_06() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'fix_basic_flg' => Cp::SETTING_DRAFT,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'set_public_date_flg' => Cp::PUBLIC_DATE_ON,
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['public_date']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_07() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'fix_basic_flg' => Cp::SETTING_DRAFT,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
        ), array(), array());

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['shipping_method']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_08() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'fix_basic_flg' => Cp::SETTING_FIX,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'shipping_method' => Cp::SHIPPING_METHOD_MESSAGE
        ), array(), array(), true);

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['announce_date']['required'], $this->equalTo(true));
    }

    public function test_checkAndSetRequired_09() {
        $cp = $this->entity('Cps', array(
            'brand_id' => $this->brand->id,
            'fix_basic_flg' => Cp::SETTING_FIX,
        ));

        $setting_basic_validator = new SettingBasicValidator(array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'permanent_flg' => Cp::PERMANENT_FLG_OFF,
        ), array(), array(), true);

        $setting_basic_validator->checkAndSetRequired();
        $validate_definition = $setting_basic_validator->getValidatorDefinition();
        $this->assertThat($validate_definition['end_date']['required'], $this->equalTo(true));
    }

    protected function tearDown() {
        $this->deleteEntities('Cps', array('brand_id' => $this->brand->id));
        $this->deleteEntities('Brands', array('id' => $this->brand->id));
    }
}
