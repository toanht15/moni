<?php

AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.CacheManager');

class CpInfoContainerTest extends BaseTest {

    protected function setUp() {
        CpInfoContainer::getInstance()->clear();
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $this->setPrivateFieldValue(new CacheManager(), "connection", new CacheManager_Connection());
        $this->clearBrandAndRelatedEntities();
    }

    public function testGetCpById01_whenEmptyId() {
        $this->assertNull(CpInfoContainer::getInstance()->getCpById(null));
    }

    public function testGetCpById02_whenNotInMemoryAndNoCache() {
        list ($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $result = CpInfoContainer::getInstance()->getCpById($cp->id);
        $this->assertEquals(
            array('cp_values' => $this->findById('Cps', $cp->id)->toArray(), 'brand_id' => $cp->getBrand()->id, 'cache_exists' => true),
            array('cp_values' => $result->toArray(),
                'brand_id' => $result->getBrand()->id,
                'cache_exists' => aafwRedisManager::getRedisInstance()->exists("cache:cpi:" . $result->id))
        );
    }

    public function testGetCpById03_whenNotInMemoryAndExistsCache() {
        list ($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $cache_manager = new CacheManager();
        $cache_manager->setCpInfo($cp->id, array(CpInfoContainer::KEY_CP => $this->findById('Cps', $cp->id)->toArray()));
        $result = CpInfoContainer::getInstance()->getCpById($cp->id);
        $this->assertEquals(
            array('cp_values' => $this->findById('Cps', $cp->id)->toArray(), 'brand_id' => $cp->getBrand()->id, 'cache_exists' => true),
            array('cp_values' => $result->toArray(),
                'brand_id' => $result->getBrand()->id,
                'cache_exists' => aafwRedisManager::getRedisInstance()->exists("cache:cpi:" . $result->id))
        );
    }

    public function testGetCpById04_whenInMemory() {
        list ($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        CpInfoContainer::getInstance()->getCpById($cp->id);
        $result = CpInfoContainer::getInstance()->getCpById($cp->id);
        $this->assertEquals(
            array('cp_values' => $this->findById('Cps', $cp->id)->toArray(), 'brand_id' => $cp->getBrand()->id, 'cache_exists' => true),
            array('cp_values' => $result->toArray(),
                'brand_id' => $result->getBrand()->id,
                'cache_exists' => aafwRedisManager::getRedisInstance()->exists("cache:cpi:" . $result->id))
        );
    }

    public function testGetCpById05_whenInvalidCpId() {
        $this->assertNull(CpInfoContainer::getInstance()->getCpById(10));
    }
}
