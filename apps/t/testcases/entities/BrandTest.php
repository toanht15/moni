<?php

AAFW::import ('jp.aainc.aafw.classes.entities.Brand');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');

class BrandTest extends BaseTest {

    const test_id = 100000;
    const test_brand_directory = 'TEST';

    public function testGetProfileImage01_exists() {
        $target = aafwEntityFactory::create("Brand");
        $IMG_URL = "test.png";
        $target->profile_img_url = $IMG_URL;

        $this->assertEquals($IMG_URL, $target->getProfileImage());
    }

    public function testGetProfileImage02_absent() {
        $target = aafwEntityFactory::create("Brand");
        $target->profile_img_url = null;
        $this->assertEquals("//static-brandcotest.com/img/icon/iconNoImage1.png", $target->getProfileImage());
    }

    public function testGetProfileImage03_empty() {
        $target = aafwEntityFactory::create("Brand");
        $target->profile_img_url = '';
        $this->assertEquals("//static-brandcotest.com/img/icon/iconNoImage1.png", $target->getProfileImage());
    }

    public function testGetColorMain01_exists() {
        $target = aafwEntityFactory::create("Brand");
        $COLOR = "#FF00FF";
        $target->color_main = $COLOR;

        $this->assertEquals($COLOR, $target->getColorMain());
    }

    public function testGetColorMain02_absent() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_main = null;
        $this->assertEquals("#CCCCCC", $target->getColorMain());
    }

    public function testGetColorMain03_empty() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_main = '';
        $this->assertEquals("#CCCCCC", $target->getColorMain());
    }

    public function testGetColorBackground01_exists() {
        $target = aafwEntityFactory::create("Brand");
        $BG_COLOR = "#FF00FF";
        $target->color_background = $BG_COLOR;

        $this->assertEquals($BG_COLOR, $target->getColorBackground());
    }

    public function testGetColorBackground02_absent() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_backgorund = null;
        $this->assertEquals('#f3f3f3', $target->getColorBackground());
    }

    public function testGetColorBackground03_empty() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_backgorund = '';
        $this->assertEquals('#f3f3f3', $target->getColorBackground());
    }

    public function testGetColorText01_exists() {
        $target = aafwEntityFactory::create("Brand");
        $COLOR_TEXT = "#FF00FF";
        $target->color_text = $COLOR_TEXT;

        $this->assertEquals($COLOR_TEXT, $target->getColorText());
    }

    public function testGetColorText02_absent() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_text = null;
        $this->assertEquals("#333333", $target->getColorText());
    }

    public function testGetColorText03_empty() {
        $target = aafwEntityFactory::create("Brand");
        $target->color_text = '';
        $this->assertEquals("#333333", $target->getColorText());
    }

    public function testGetBackgroundImageRepeatType01_typeRepeat() {
        $target = aafwEntityFactory::create("Brand");
        $target->background_img_x = 1;
        $target->background_img_y = 1;

        $this->assertEquals(Brand::BACKGROUND_IMAGE_REPEAT_TYPE_REPEAT, $target->getBackgroundImageRepeatType());
    }

    public function testGetBackgroundImageRepeatType02_typeX() {
        $target = aafwEntityFactory::create("Brand");
        $target->background_img_x = 1;
        $target->background_img_y = 0;

        $this->assertEquals(Brand::BACKGROUND_IMAGE_REPEAT_TYPE_X, $target->getBackgroundImageRepeatType());
    }

    public function testGetBackgroundImageRepeatType03_typeY() {
        $target = aafwEntityFactory::create("Brand");
        $target->background_img_x = 0;
        $target->background_img_y = 1;

        $this->assertEquals(Brand::BACKGROUND_IMAGE_REPEAT_TYPE_Y, $target->getBackgroundImageRepeatType());
    }

    public function testGetBackgroundImageRepeatType04_none() {
        $target = aafwEntityFactory::create("Brand");
        $target->background_img_x = 0;
        $target->background_img_y = 0;

        $this->assertEquals(Brand::BACKGROUND_IMAGE_REPEAT_TYPE_NO, $target->getBackgroundImageRepeatType());
    }

    public function testHasFreeArea01_exists() {
        $brand = $this->entity("Brands");
        $entry = $this->entity("FreeAreaEntries", array("brand_id" => $brand->id, "public_flg" => '1'));

        // 複雑なオブジェクトのテストは、直接プロパティ同士を比較せずに、配列化して文字列で比較します。
        $freeArea = $brand->hasFreeArea();
        $this->assertEquals($entry->toArray(), array('id' => $freeArea->id, 'brand_id' => $freeArea->brand_id, 'public_flg' => $freeArea->public_flg));
    }

    public function testHasFreeArea02_absent() {
        $brand = $this->entity("Brands");
        $this->entity("FreeAreaEntries", array("brand_id" => $brand->id, "public_flg" => 0));

        $this->assertNull($brand->hasFreeArea());
    }

    public function testHasOption01_exists() {
        $brand = $this->entity("Brands");
        $this->entity("BrandOptions", array("brand_id" => $brand->id, "option_id" => 1));

        $this->assertTrue($brand->hasOption(1));
    }

    public function testHasOption02_absent() {
        $brand = $this->entity("Brands");
        $this->assertFalse($brand->hasOption(1));
    }

    public function testHasOption03_existsWithArgs() {
        $brand = $this->entity("Brands");
        $brand_options = array($this->entity("BrandOptions", array("brand_id" => $brand->id)));
        $this->assertFalse($brand->hasOption(1, $brand_options));
    }

    public function testGetUrl01_notSecure() {
        $brand = $this->entity("Brands", array("directory_name" => self::test_brand_directory));
        $this->assertEquals("http://brandcotest.com/".self::test_brand_directory."/", $brand->getUrl());
    }

    public function testGetUrl02_secure() {
        $brand = $this->entity("Brands", array("directory_name" => self::test_brand_directory));
        $this->assertEquals("https://brandcotest.com/".self::test_brand_directory."/", $brand->getUrl(true));
    }

    public function testIsClosedBrand01_notCLosed() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "9999-12-31 23:59:59", "display_end_date" => "9999-12-31 23:59:59"));
        $this->assertThat($brand->getCloseStatus(), $this->equalTo(BrandContracts::MODE_OPEN));
    }

    public function testIsClosedBrand02_cLosed() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2005-12-31 23:59:59", "display_end_date" => '9999-12-31 23:59:59'));
        $this->assertThat($brand->getCloseStatus(), $this->equalTo(BrandContracts::MODE_CLOSED));
    }

    public function testIsClosedBrand03_nonDisplay() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2005-12-31 23:59:59", "display_end_date" => '2006-12-31 23:59:59'));
        $this->assertThat($brand->getCloseStatus(), $this->equalTo(BrandContracts::MODE_SITE_CLOSED));
    }

    public function testIsPlan01_whenPlanAndNoArg() {
        $brand = $this->entity("Brands");
        $plan_id = 1;
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "plan" => $plan_id));

        $this->assertTrue($brand->isPlan($plan_id));
    }

    public function testIsPlan02_whenNoPlanAndNoArg() {
        $brand = $this->entity("Brands");
        $plan_id = 1;
        $this->entity("BrandContracts", array("brand_id" => $brand->id));

        $this->assertFalse($brand->isPlan($plan_id));
    }

    public function testIsPlan03_whenPlanAndArg() {
        $brand = $this->entity("Brands");
        $plan_id = 1;
        $brand_contract = $this->entity("BrandContracts", array("brand_id" => $brand->id, "plan" => $plan_id));

        $this->assertTrue($brand->isPlan($plan_id, $brand_contract));
    }

    public function testIsLimitedBrandPage01_whenIsLimitedAndNoArg() {
        $brand = $this->entity("Brands");
        $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id, "name" => BrandGlobalSettingService::NEW_PAGE_LABEL, "content" => 1));
        $this->assertTrue($brand->isLimitedBrandPage());
    }

    public function testIsLimitedBrandPage02_whenIsNotLimitedAndNoArg() {
        $brand = $this->entity("Brands");
        $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id, "name" => BrandGlobalSettingService::TOP_PANEL_FULL_TEXT));
        $this->assertFalse($brand->isLimitedBrandPage());
    }

    public function testIsLimitedBrandPage03_whenIsNotLimitedAndArg() {
        $brand = $this->entity("Brands");
        $brand_global_setting = $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id, "name" => BrandGlobalSettingService::TOP_PANEL_FULL_TEXT));
        $this->assertFalse($brand->isLimitedBrandPage($brand_global_setting));
    }

    public function testIsClosedBrand01_whenIsClosedAndNoArg() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2005-12-31 23:59:59", "display_end_date" => '2006-12-31 23:59:59'));
        $this->assertTrue($brand->isClosedBrand());
    }

    public function testIsClosedBrand02_whenIsNotClosedAndNoArg() {
        $brand = $this->entity("Brands");
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2999-12-31 23:59:59", "display_end_date" => '2999-12-31 23:59:59'));
        $this->assertFalse($brand->isClosedBrand());
    }

    public function testIsClosedBrand03_whenIsClosedAndArg() {
        $brand = $this->entity("Brands");
        $brand_contarct = $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "2005-12-31 23:59:59", "display_end_date" => '2006-12-31 23:59:59'));
        $this->assertTrue($brand->isClosedBrand($brand_contarct));
    }
}