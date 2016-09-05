<?php
AAFW::import ('jp.aainc.classes.services.BrandContractService');

class BrandContractServiceTest extends BaseTest {

    /** @var  BrandContractService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandContractService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }

    public function test_GetEmptyBrandContract() {
        $brand_contract = $this->target->getEmptyBrandContract();
        $this->assertNull($brand_contract->id);
    }

    /**
     * brand_contract_id を渡す
     */
    public function test_getBrandContract() {
        $brand = $this->entity('Brands');
        $brand_contracts = $this->target->getBrandContract($brand->id);
        foreach($brand_contracts as $brand_contract) {
            $this->assertThat($brand_contract->id, $this->equalTo(BrandTest::test_id));
        }
    }

    public function test_getBrandContractByBrandId() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'contract_end_date' => '2015-05-01 00:00:00'));
        $brand_contracts = $this->target->getBrandContractByBrandId($brand->id);
        $this->assertEquals(array('brand_id' => $brand->id, 'contract_end_date' => '2015-05-01 00:00:00'), array('brand_id' => $brand_contracts->brand_id, 'contract_end_date' => $brand_contracts->contract_end_date));
    }

    public function test_updateBrandContract() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $brand_contract = $this->entity('BrandContracts', array(
            'brand_id' => $brand->id,
            'contract_end_date' => '2015-05-01 00:00:00'
        ));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $result = $this->target->updateBrandContract($brand_contract);

        $this->assertThat($brand->id, $this->equalTo($result->brand_id));
        $this->assertThat('2015-05-01 00:00:00', $this->equalTo($result->contract_end_date));
        $this->assertEquals(false, aafwRedisManager::getRedisInstance()->exists("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id));
    }

    public function test_getClosedBrandContract01_Match() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'contract_end_date' => '2015-05-01 00:00:00', 'delete_status' => BrandContracts::MODE_OPEN));
        $result = $this->target->getClosedBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertEquals('2015-05-01 00:00:00', $result->toArray()[0]->contract_end_date);
        }
    }

    public function test_getClosedBrandContract02_NotMatch() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'contract_end_date' => '9999-05-01 00:00:00', 'delete_status' => BrandContracts::MODE_OPEN));
        $result = $this->target->getClosedBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertNotEquals('1', $result->toArray()[0]->brand_id);
        }
    }

    public function test_getSiteClosedBrandContract01_Match() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'display_end_date' => '2015-05-01 00:00:00', 'delete_status' => BrandContracts::MODE_CLOSED));
        $result = $this->target->getSiteClosedBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertEquals('2015-05-01 00:00:00', $result->toArray()[0]->display_end_date);
        }
    }

    public function test_getSiteClosedBrandContract02_NotMatch() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'display_end_date' => '9999-05-01 00:00:00', 'delete_status' => BrandContracts::MODE_CLOSED));
        $result = $this->target->getSiteClosedBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertNotEquals('1', $result->toArray()[0]->brand_id);
        }
    }

    public function test_getDeleteUserInfoBrandContract01_Match() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'display_end_date' => '2015-02-23 00:00:00', 'delete_status' => BrandContracts::MODE_SITE_CLOSED));
        $result = $this->target->getDeleteUserInfoBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertEquals('2015-02-23 00:00:00', end($result->toArray())->display_end_date);
        }
    }

    public function test_getDeleteUserInfoBrandContract02_NotMatch() {
        $brand = $this->entity('Brands');
        $this->deleteEntities('BrandContracts');
        $this->entity('BrandContracts', array('brand_id' => $brand->id, 'display_end_date' => '2015-02-29 00:00:00', 'delete_status' => BrandContracts::MODE_SITE_CLOSED));
        $result = $this->target->getDeleteUserInfoBrandContract();
        if (!count($result)) {
            $this->assertEquals(array(), $result);
        }else{
            $this->assertNotEquals('2015-02-29 00:00:00', $result->toArray()[0]->display_end_date);
        }
    }

}