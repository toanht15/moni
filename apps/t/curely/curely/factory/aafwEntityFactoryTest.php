<?php
AAFW::import('jp.aainc.aafw.factory.aafwEntityFactory');

class aafwEntityFactoryTest extends BaseTest {

    /**
     * create test
     * @test
     */
    public function createTest() {
        $entity = aafwEntityFactory::create('Brand');
        $result = $entity->getRelations();
        $this->assertThat($result['BrandContracts']['id'], $this->equalTo('brand_id'));
    }
}
