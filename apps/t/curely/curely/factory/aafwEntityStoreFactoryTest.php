<?php
AAFW::import('jp.aainc.aafw.factory.aafwEntityStoreFactory');

class aafwEntityStoreFactoryTest extends BaseTest {

    /**
     * create test
     * @test
     */
    public function createTest() {
        $store = aafwEntityStoreFactory::create('Brands');
        $this->assertThat($store->getEntityName(), $this->equalTo('Brand'));
    }
}
