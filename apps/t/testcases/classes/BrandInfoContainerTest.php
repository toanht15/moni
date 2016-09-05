<?php

AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.CacheManager');

class BrandInfoContainerTest extends BaseTest {

    protected function setUp() {
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $this->setPrivateFieldValue(new CacheManager(), "connection", new CacheManager_Connection());
    }

    public function testGetInstance01_whenFirstCall() {
        $this->assertTrue(BrandInfoContainer::getInstance() instanceof BrandInfoContainer);
    }

    public function testGetInstance02_whenSecondCall() {
        BrandInfoContainer::getInstance();
        $this->assertTrue(BrandInfoContainer::getInstance() instanceof BrandInfoContainer);
    }

    public function testInitialize01_whenExist() {
        $brand = $this->emptyEntity('Brands');
        $brand->id = 1;
        $target = BrandInfoContainer::getInstance();
        $target->initialize($brand);

        $cache = $this->getPrivateFieldValue($target, "cache");
        $brand = $this->getPrivateFieldValue($target, "brand");
        $this->assertEquals(
            array("brand" => $brand, "brand" => $brand),
            array("brand" => $cache[BrandInfoContainer::KEY_BRAND], "brand" => $brand));
    }

    public function testInitialize02_whenNull() {
        try {
            BrandInfoContainer::getInstance()->initialize(null);
            $this->fail();
        } catch(aafwException $e) {
            $this->assertTrue($e != null);
        }
    }

    public function testGetBrand01_whenExist() {
        $brand = $this->emptyEntity('Brands');
        $brand->id = 1;
        $target = BrandInfoContainer::getInstance();

        $target->initialize($brand);

        $this->assertEquals($brand, $target->getBrand());
    }

    public function testGetBrand02_whenNull() {
        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);

        $this->assertNull(BrandInfoContainer::getInstance()->getBrand());
    }

    public function testGetBrandContract01_whenNotCachedAndNotInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_contract = $this->entity("BrandContracts", array("brand_id" => $new_brand->id));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandContract();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_contract_id" => $brand_contract->id, "cache" => $brand_contract->id),
            array("brand_contract_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_CONTRACT]['id']));
    }

    public function testGetBrandContract02_whenNotCachedAndInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_contract = $this->entity("BrandContracts", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_CONTRACT => $brand_contract->toArray()));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandContract();

        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_contract_id" => $brand_contract->id, "cache" => $brand_contract->id),
            array("brand_contract_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_CONTRACT]['id']));
    }

    public function testGetBrandContract03_whenCached() {
        $new_brand = $this->entity("Brands");
        $brand_contract = $this->entity("BrandContracts", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_CONTRACT => $brand_contract->toArray()));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $target->getBrandContract();
        $result = $target->getBrandContract();

        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_contract_id" => $brand_contract->id, "cache" => $brand_contract->id),
            array("brand_contract_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_CONTRACT]['id']));
    }

    public function testGetBrandContract04_whenNull() {
        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);

        $this->assertNull(BrandInfoContainer::getInstance()->getBrandContract());
    }

    public function testGetBrandPageSetting01_whenNotCachedAndNotInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_page_setting = $this->entity("BrandPageSettings", array("brand_id" => $new_brand->id));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandPageSetting();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_page_setting_id" => $brand_page_setting->id, "cache" => $brand_page_setting->id),
            array("brand_page_setting_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_PAGE_SETTING]['id']));
    }

    public function testGetBrandPageSetting02_whenNotCachedInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_page_setting = $this->entity("BrandPageSettings", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_PAGE_SETTING => $brand_page_setting->toArray()));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandPageSetting();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_page_setting_id" => $brand_page_setting->id, "cache" => $brand_page_setting->id),
            array("brand_page_setting_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_PAGE_SETTING]['id']));
    }

    public function testGetBrandPageSetting03_whenCached() {
        $new_brand = $this->entity("Brands");
        $brand_page_setting = $this->entity("BrandPageSettings", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_PAGE_SETTING => $brand_page_setting->toArray()));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $target->getBrandPageSetting();
        $result = $target->getBrandPageSetting();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_page_setting_id" => $brand_page_setting->id, "cache" => $brand_page_setting->id),
            array("brand_page_setting_id" => $result->id, "cache" => $cache[BrandInfoContainer::KEY_BRAND_PAGE_SETTING]['id']));
    }

    public function testGetBrandOptions01_whenNotCachedAndNotInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_option = $this->entity("BrandOptions", array("brand_id" => $new_brand->id));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandOptions();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_option_id" => array($brand_option->id), "cache" => array($brand_option->id)),
            array("brand_option_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_OPTIONS][0]['id'])));
    }

    public function testGetBrandOptions02_whenNotCachedInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_option = $this->entity("BrandOptions", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_OPTIONS => array($brand_option->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandOptions();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_option_id" => array($brand_option->id), "cache" => array($brand_option->id)),
            array("brand_option_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_OPTIONS][0]['id'])));
    }

    public function testGetBrandOptions03_whenCached() {
        $new_brand = $this->entity("Brands");
        $brand_option = $this->entity("BrandOptions", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_OPTIONS => array($brand_option->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $target->getBrandOptions();
        $result = $target->getBrandOptions();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_option_id" => array($brand_option->id), "cache" => array($brand_option->id)),
            array("brand_option_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_OPTIONS][0]['id'])));
    }

    public function testGetBrandGlobalSettings01_whenNotCachedAndNotInNotRedis() {
        $new_brand = $this->entity("Brands");
        $brand_global_setting = $this->entity("BrandGlobalSettings", array("brand_id" => $new_brand->id));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandGlobalSettings();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_setting_id" => array($brand_global_setting->id), "cache" => array($brand_global_setting->id)),
            array("brand_global_setting_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_SETTINGS][0]['id'])));
    }

    public function testGetBrandGlobalSettings02_whenNotCachedAndNotInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_global_setting = $this->entity("BrandGlobalSettings", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_GLOBAL_SETTINGS => array($brand_global_setting->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandGlobalSettings();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_setting_id" => array($brand_global_setting->id), "cache" => array($brand_global_setting->id)),
            array("brand_global_setting_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_SETTINGS][0]['id'])));
    }

    public function testGetBrandGlobalSettings03_whenCached() {
        $new_brand = $this->entity("Brands");
        $brand_global_setting = $this->entity("BrandGlobalSettings", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_GLOBAL_SETTINGS => array($brand_global_setting->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $target->getBrandGlobalSettings();
        $result = $target->getBrandGlobalSettings();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_setting_id" => array($brand_global_setting->id), "cache" => array($brand_global_setting->id)),
            array("brand_global_setting_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_SETTINGS][0]['id'])));
    }

    public function testGetBrandGlobalMenus01_whenNotCachedAndNotInNotRedis() {
        $new_brand = $this->entity("Brands");
        $brand_global_menu = $this->entity("BrandGlobalMenus", array("brand_id" => $new_brand->id));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandGlobalMenus();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_menu_id" => array($brand_global_menu->id), "cache" => array($brand_global_menu->id)),
            array("brand_global_menu_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_MENUS][0]['id'])));
    }


    public function testGetBrandGlobalMenus02_whenNotCachedAndNotInRedis() {
        $new_brand = $this->entity("Brands");
        $brand_global_menu = $this->entity("BrandGlobalMenus", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_GLOBAL_MENUS => array($brand_global_menu->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $result = $target->getBrandGlobalMenus();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_menu_id" => array($brand_global_menu->id), "cache" => array($brand_global_menu->id)),
            array("brand_global_menu_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_MENUS][0]['id'])));
    }

    public function testGetBrandGlobalMenus03_whenCached() {
        $new_brand = $this->entity("Brands");
        $brand_global_menu = $this->entity("BrandGlobalMenus", array("brand_id" => $new_brand->id));

        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array(BrandInfoContainer::KEY_BRAND_GLOBAL_MENUS => array($brand_global_menu->toArray())));

        $target = BrandInfoContainer::getInstance();
        $this->setPrivateFieldValue($target, "cache", null);
        $this->setPrivateFieldValue($target, "brand", null);
        $target->initialize($new_brand);

        $target->getBrandGlobalMenus();
        $result = $target->getBrandGlobalMenus();

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);

        $this->assertEquals(
            array("brand_global_menu_id" => array($brand_global_menu->id), "cache" => array($brand_global_menu->id)),
            array("brand_global_menu_id" => array($result->toArray()[0]->id), "cache" => array($cache[BrandInfoContainer::KEY_BRAND_GLOBAL_MENUS][0]['id'])));
    }

    public function testClear01_whenNotCached() {
        $brand_id = 1;
        BrandInfoContainer::getInstance()->clear($brand_id);
    }

    public function testClear02_whenCached() {
        $new_brand = $this->entity("Brands");
        $cache_manager = new CacheManager();
        $cache_manager->setBrandInfo($new_brand->id, array());
        BrandInfoContainer::getInstance()->clear($new_brand->id);

        $cache_manager = new CacheManager();
        $cache = $cache_manager->getBrandInfo($new_brand->id);
        $this->assertEquals(null, $cache);
    }
}
