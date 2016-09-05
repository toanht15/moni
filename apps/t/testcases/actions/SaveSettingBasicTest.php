<?php
AAFW::import('jp.aainc.actions.user.brandco.admin-cp.save_setting_basic');
AAFW::import('jp.aainc.classes.services.ManagerService');

class SaveSettingBasicTest extends BaseTest {

    private $p;

    public function setup() {
        $brand = $this->entity('Brands');
        BrandInfoContainer::getInstance()->initialize($brand);

        $this->p = array(
            'public_date' => '9999/12/29',
            'publicTimeHH' => '23',
            'publicTimeMM' => '59',
            'start_date' => '9999/12/29',
            'openTimeHH' => '23',
            'openTimeMM' => '59',
            'show_recruitment_note' => Cp::FLAG_HIDE_VALUE,
            'join_limit_sns_flg' => Cp::JOIN_LIMIT_SNS_OFF,
            'restricted_age_flg' => Cp::CP_RESTRICTED_AGE_FLG_OFF,
            'restricted_gender_flg' => Cp::CP_RESTRICTED_GENDER_FLG_OFF,
            'restricted_address_flg' => Cp::CP_RESTRICTED_ADDRESS_FLG_OFF,
            'title' => 'test',
            'use_cp_page_close_flg' => 0
        );
    }

    public function testSaveSettingBasic01_DefaultCp() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_FIRST, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'announce_date' => '9999/12/31',
            'announceTimeHH' => '23',
            'announceTimeMM' => '59',
            'end_date' => '9999/12/30',
            'closeTimeDate' => 1,
            'show_winner_label' => Cp::FLAG_HIDE_VALUE,
            'winner_count' => 1,
            'shipping_method' => Cp::SHIPPING_METHOD_MESSAGE,
            'announce_display_label_use_flg' => 0
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }

    public function testSaveSettingBasic02_NonIncentiveDefaultCp() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_NON_INCENTIVE, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'announce_date' => '9999/12/31',
            'announceTimeHH' => '23',
            'announceTimeMM' => '59',
            'end_date' => '9999/12/30',
            'closeTimeDate' => 1
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }

    public function testSaveSettingBasic03_NonIncentiveDefaultCp_Failed() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_NON_INCENTIVE, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'announce_date' => '9999/12/31',
            'announceTimeHH' => '23',
            'announceTimeMM' => '59',
            'end_date' => '9999/12/28',
            'closeTimeDate' => 1
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

         $this->assertFalse($save_setting_basic->validate());
    }

    public function testSaveSettingBasic04_NonIncentivePermanentCp() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_NON_INCENTIVE, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'permanent_flg' => Cp::PERMANENT_FLG_ON
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }

    public function testSaveSettingBasic05_NonIncentivePermanentCp() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_NON_INCENTIVE, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_FIX,
            'announce_date' => '9999/12/31',
            'announceTimeHH' => '23',
            'announceTimeMM' => '59',
            'end_date' => '9999/12/30',
            'closeTimeDate' => 1,
            'permanent_flg' => Cp::PERMANENT_FLG_ON
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }
    
    public function testSaveSettingBasic06_DefaultCp_DraftSave() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_FIRST, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_DRAFT,
            'announce_date' => '9999/12/31',
            'announceTimeHH' => '23',
            'announceTimeMM' => '59',
            'end_date' => '9999/12/30',
            'closeTimeDate' => 1,
            'permanent_flg' => Cp::PERMANENT_FLG_OFF,
            'show_winner_label' => Cp::FLAG_HIDE_VALUE,
            'winner_count' => 1,
            'shipping_method' => Cp::SHIPPING_METHOD_MESSAGE,
            'announce_display_label_use_flg' => 0
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }

    public function testSaveSettingBasic07_NonIncentivePermanentCp_DraftSave() {
        $cp = $this->entity("Cps", array("brand_id" => BrandInfoContainer::getInstance()->getBrand()->id, "type" => 1, 'selection_method' => CpCreator::ANNOUNCE_NON_INCENTIVE, "recruitment_note" => "", "extend_tag" => "", "image_url" => "https://hoge"));
        $save_setting_basic = new save_setting_basic();

        $adding_p = array(
            'cp_id' => $cp->id,
            'save_type' => Cp::SETTING_DRAFT,
            'permanent_flg' => Cp::PERMANENT_FLG_ON
        );
        $save_setting_basic->rewriteParams(array_merge($this->p, $adding_p));

        $this->assertTrue($save_setting_basic->validate());
    }
}