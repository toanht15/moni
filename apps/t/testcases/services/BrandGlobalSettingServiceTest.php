<?php
AAFW::import ('jp.aainc.classes.services.BrandGlobalSettingService');

class BrandGlobalSettingServiceTest extends BaseTest {

    /** @var  BrandGlobalSettingService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandGlobalSettingService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }

    public function testGetBrandGlobalSettingByName01_whenMatched() {
        $setting1 = new BrandGlobalSetting();
        $setting1->name = "name1";
        $setting2 = new BrandGlobalSetting();
        $setting2->name = "name2";

        $this->assertEquals($setting1, $this->target->getBrandGlobalSettingByName(array($setting1, $setting2), "name1"));
    }

    public function testGetBrandGlobalSettingByName02_whenNotMatched() {
        $setting1 = new BrandGlobalSetting();
        $setting1->name = "name1";
        $setting2 = new BrandGlobalSetting();
        $setting2->name = "name2";

        $this->assertNull($this->target->getBrandGlobalSettingByName(array($setting1, $setting2), "name3"));
    }

    public function testGetBrandGlobalSettingByBrandId01_whenMatched() {
        $brand = $this->entity("Brands");
        $brand_global_setting1 = $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id));
        $brand_global_setting2 = $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id));

        $result = $this->target->getBrandGlobalSettingsByBrandId($brand->id)->toArray();

        $this->assertEquals(
            array(2, $brand_global_setting1->id, $brand_global_setting2->id),
            array(count($result), $result[0]->id, $result[1]->id));
    }

    public function testGetBrandGlobalSettingByBrandId02_whenNotMatched() {
        $brand = $this->entity("Brands");

        $this->assertEquals(array(), $this->target->getBrandGlobalSettingsByBrandId($brand->id));
    }

    public function testGetBrandGlobalSettingByBrandId03_whenNull() {
        $this->assertNull($this->target->getBrandGlobalSettingsByBrandId(null));
    }

    public function testChangeHideFanListMessageManual01_whenViewed() {
        $brand = $this->entity('Brands');
        $this->entity("BrandGlobalSettings", array("brand_id" => $brand->id, "name" => BrandGlobalSettingService::HIDE_FAN_LIST_MESSAGE_MANUAL_KEY));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->changeHideFanListMessageManual($brand->id, BrandGlobalSettingService::VIEW_FAN_LIST_MESSAGE_MANUAL);

        $this->assertEquals(
            array(false, 0),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                  $this->countEntities("BrandGlobalSettings", array("brand_id" => $brand->id))));
    }

    public function testChangeHideFanListMessageManual02_whenHidden() {
        $brand = $this->entity('Brands');
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->changeHideFanListMessageManual($brand->id, BrandGlobalSettingService::HIDE_FAN_LIST_MESSAGE_MANUAL);

        $this->assertEquals(
            array(false, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                $this->countEntities("BrandGlobalSettings", array("brand_id" => $brand->id))));
    }

    public function testChangeHideFanListMessageManual03_whenNull() {
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->changeHideFanListMessageManual(1, null);

        $this->assertEquals("TEST",aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id));
    }

    public function testSaveGlobalSetting01_whenInsert() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");
        $brand_global_setting = $this->emptyObjectOf("BrandGlobalSettings", array("brand_id" => $brand->id));

        $this->target->saveGlobalSetting($brand_global_setting);

        $this->assertEquals(
            array(false, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                  $this->countEntities("BrandGlobalSettings", array("brand_id" => $brand->id))));
    }
}