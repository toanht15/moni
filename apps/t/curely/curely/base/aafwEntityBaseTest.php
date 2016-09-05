<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class aafwEntityBaseTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwEntityFactory::create('Brand');
    }

    /**
     * @test
     */
    public function relationTest() {
        $result = $this->target->getRelations();
        $this->assertThat($result['BrandContracts']['id'], $this->equalTo('brand_id'));
    }
}
