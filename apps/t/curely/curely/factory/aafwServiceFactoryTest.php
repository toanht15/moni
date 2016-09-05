<?php
AAFW::import('jp.aainc.aafw.factory.aafwServiceBase');

class aafwServiceFactoryTest extends BaseTest {

    /**
     * create test
     * @test
     */
    public function createTest() {
        $service_factory = new aafwServiceFactory();
        $sample_service = $service_factory->create('BrandService');
        $this->assertThat($sample_service->getClassPath(), $this->equalTo('jp.aainc.classes.services.BrandService'));
    }
}

