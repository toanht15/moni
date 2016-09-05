<?php
AAFW::import ('jp.aainc.classes.services.BrandPageSettingService');

class BrandPageSettingServiceTest extends BaseTest {

    /** @var  BrandPageSettingService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandPageSettingService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }

    public function testSetPublicPageSettings01_whenNotExist() {
        $brand = $this->entity("Brands");
        $public_setting = 1;
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setPublicPageSettings($brand->id, $public_setting);

        $this->assertEquals(
            array(false, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $this->countEntities("BrandPageSettings", array("brand_id" => $brand->id)))
        );
    }

    public function testSetPublicPageSettings02_whenExist() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        $public_setting = 1;
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setPublicPageSettings($brand->id, $public_setting);

        $this->assertEquals(
            array(false, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $this->countEntities("BrandPageSettings", array("brand_id" => $brand->id)))
        );
    }

    public function testSetPageMetaSetting01_whenNoExist() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setPageMetaSetting($brand->id, array("meta_title" => "TITLE", "meta_description" => "DESC", "meta_keyword" => "KEYWORD", "og_image_url" => "URL"));

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "TITLE", "DESC", "KEYWORD", "URL"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                 $result->meta_title, $result->meta_description, $result->meta_keyword, $result->og_image_url));
    }

    public function testSetPageMetaSetting02_whenExist() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setPageMetaSetting($brand->id, array("meta_title" => "TITLE", "meta_description" => "DESC", "meta_keyword" => "KEYWORD", "og_image_url" => "URL"));

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "TITLE", "DESC", "KEYWORD", "URL"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                $result->meta_title, $result->meta_description, $result->meta_keyword, $result->og_image_url));
    }

    public function testSetTagPageSetting01_whenNotExist() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setTagPageSettings($brand->id, "TAG_TEXT");

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "TAG_TEXT"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->tag_text));
    }

    public function testSetTagPageSetting02_whenExist() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setTagPageSettings($brand->id, "TAG_TEXT");

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "TAG_TEXT"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->tag_text));
    }

    public function testSetRequiredPrivacySettings01_whenNotExistAndGetAddress() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setRequiredPrivacySettings(
            $brand->id,
            array("privacy_required_name", "privacy_required_sex", "privacy_required_birthday", "privacy_required_address",
                  "privacy_required_tel", "privacy_required_restricted", "privacy_required_address"),
            123);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, 1, 1, 1, 123, 1, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                $result->privacy_required_name, $result->privacy_required_sex, $result->privacy_required_birthday, $result->privacy_required_address,
                $result->privacy_required_tel, $result->privacy_required_restricted));
    }

    public function testSetRequiredPrivacySettings02_whenExistAndNotGetAddress() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setRequiredPrivacySettings(
            $brand->id,
            array("privacy_required_name", "privacy_required_sex", "privacy_required_birthday", "privacy_required_address",
                "privacy_required_tel", "privacy_required_restricted"), 0);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, 1, 1, 1, 0, 1, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                $result->privacy_required_name, $result->privacy_required_sex, $result->privacy_required_birthday, $result->privacy_required_address,
                $result->privacy_required_tel, $result->privacy_required_restricted));
    }

    public function testSetAgreementSettings01_whenNotExist() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setAgreementSettings($brand->id, "AGREEMENT", BrandPageSetting::SHOW_AGREEMENT_CHECKBOX);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "AGREEMENT"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->agreement));
    }

    public function testSetAgreementSettings02_whenExist() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->setAgreementSettings($brand->id, "AGREEMENT", BrandPageSetting::SHOW_AGREEMENT_CHECKBOX);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "AGREEMENT"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->agreement));
    }

    public function testSetRestrictedAgeSettings01_whenNotExist() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $restricted_age = 25;
        $this->target->setRestrictedAgeSettings($brand->id, $restricted_age);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, $restricted_age),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->restricted_age));
    }

    public function testSetRestrictedAgeSettings02_whenExist() {
        $brand = $this->entity("Brands");
        $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $restricted_age = 25;
        $this->target->setRestrictedAgeSettings($brand->id, $restricted_age);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, $restricted_age),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->restricted_age));
    }

    public function testUpdateBrandPageSetting01_whenSuccess() {
        $brand = $this->entity("Brands");
        $brand_page_setting = $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $brand_page_setting->tag_text = "NEW";
        $this->target->updateBrandPageSetting($brand_page_setting);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "NEW"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->tag_text));
    }

    public function testUpdateBrandPageSetting02_whenFailure() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $brand_page_setting = $this->emptyObjectOf("BrandPageSettings");

        $this->target->updateBrandPageSetting($brand_page_setting);

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array("TEST", null),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result));
    }

    public function testUpdateTopPageUrl() {
        $brand = $this->entity("Brands");
        $brand_page_setting = $this->entity("BrandPageSettings", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->updateTopPageUrl($brand_page_setting, "NEW");

        $result = $this->findOne("BrandPageSettings", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, "NEW"),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->top_page_url));
    }
}