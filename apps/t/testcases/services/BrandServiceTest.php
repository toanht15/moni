<?php
AAFW::import ('jp.aainc.classes.services.BrandService');

class BrandServiceTest extends BaseTest {

    /** @var  BrandService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $this->clearBrandAndRelatedEntities();
        aafwEntityStoreBase::clearInTransactions();
    }

    public function testGetAllBrand01_whenAllOfEntitiesHasBeenLogicallyDeleted() {
        $result = $this->target->getAllBrands();
        $this->assertEquals(array(), $result);
    }

    public function testGetAllBrand02_whenOneEntityExists() {
        $this->entity('Brands');
        $result = $this->target->getAllBrands();
        $this->assertEquals(1, $result->total());
    }

    public function testGetAllBrand03_whenTwoEntitiesExist() {
        $this->entity('Brands', array('test_page' => 0));
        $this->entity('Brands', array('test_page' => 0));
        $result = $this->target->getAllBrands();
        $this->assertEquals(2, $result->total());
    }

    public function testGetAllPublicBrand01_whenThereNoPublicBrands() {
        $this->entity('Brands', array('test_page' => 1));
        $result = $this->target->getAllPublicBrand();
        $this->assertEquals(array(), $result);
    }

    public function testGetAllPublicBrand02_whenThereOnePublicBrands() {
        $this->entity('Brands', array('test_page' => 1));
        $this->entity('Brands', array('test_page' => 0));
        $result = $this->target->getAllPublicBrand();
        $this->assertEquals(1, $result->total());
    }

    public function testGetAllPublicBrand03_whenThereTwoPublicBrands() {
        $this->entity('Brands', array('test_page' => 1));
        $this->entity('Brands', array('test_page' => 0));
        $this->entity('Brands', array('test_page' => 0));
        $result = $this->target->getAllPublicBrand();
        $this->assertEquals(2, $result->total());
    }

    public function testUpdateBrand01_whenSuccess() {
        $brand = $this->entity('Brands');
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $brand->name = "test";
        $this->target->updateBrand($brand);

        $result = $this->findOne("Brands", array("id" => $brand->id));
        $this->assertEquals(array(false, "test"), array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->name));
    }

    public function testUpdateBrand02_whenFailure() {
        $brand = $this->entity("Brands");
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        try {
            $cache_manager = $this->getPrivateFieldValue(BrandInfoContainer::getInstance(), "cache_manager");
            $connection = $this->getPrivateFieldValue($cache_manager, "connection");
            $redis = $connection->getRedis();
            $redis->close();

            $brand->name = "NEW";
            $this->target->updateBrand($brand);

            $result = $this->findOne("Brands", array("id" => $brand->id));
            $this->assertEquals(
                array("TEST", ""),
                array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->name)
            );
        } finally {
            $this->setPrivateFieldValue(new CacheManager(), "connection", null);
        }
    }

    public function testUpdateBrandList01() {
        $brand = $this->entity('Brands');
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $brand->name = "test";
        $this->target->updateBrandList($brand);

        $result = $this->findOne("Brands", array("id" => $brand->id));
        $this->assertEquals(array(false, "test"), array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $result->name));
    }

    public function testRefreshBrandOptions01() {
        $brand = $this->entity('Brands');
        $this->entity("BrandOptions", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->refreshBrandOptions($brand->id, array("option_1" => "1", "option_2" => "0"));

        $option = $this->findOne("BrandOptions", array("brand_id" => $brand->id));
        $this->assertEquals(
            array(false, 1),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id), $option->option_id));
    }
}