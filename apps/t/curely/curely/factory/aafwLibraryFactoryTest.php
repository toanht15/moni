<?php
AAFW::import('jp.aainc.aafw.factory.aafwLibraryFactory');

class aafwLibraryFactoryTest extends BaseTest {

    /**
     * create test
     * class path から class を import する
     * @test
     */
    public function createTest() {
        $library = aafwLibraryFactory::create('jp.aainc.classes.services.ApplicationService');
        $this->assertThat(get_class($library), $this->equalTo('ApplicationService'));
    }
}
