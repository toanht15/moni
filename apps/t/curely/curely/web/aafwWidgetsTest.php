<?php
AAFW::import('jp.aainc.lib.web.aafwWidgets');

class aafwWidgetsTest extends BaseTest {

    /** @var aafwWidgets $target */
    private $target;

    public function setUp() {
        $this->target = aafwWidgets::getInstance();
    }

    /**
     * @test
     */
    public function getInstanceTest() {
        $result = aafwWidgets::getInstance();
        $this->assertTrue($result instanceof aafwWidgets);
    }

    /**
     * @test
     */
    public function loadWidgetTest() {
        $result = $this->target->loadWidget('CpPhotoList');
        $this->assertTrue($result instanceof CpPhotoList);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function loadWidget_Error_Test() {
        $this->target->loadWidget('');
    }
}
