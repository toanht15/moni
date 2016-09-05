<?php
require_once __DIR__ . '/../../../config/define.php';
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.aafw.base.aafwException');

class aafwApplicationConfigTest extends PHPUnit_Framework_TestCase {

    /** @var aafwApplicationConfig $target */
    private $target;
    private $yml;

    public function setUp() {
        $this->target = aafwApplicationConfig::getInstance();
        $this->yml = AAFW_DIR . '/config/app.yml';
    }

    /**
     * @test
     */
    public function getInstanceTest() {
        $application_config = aafwApplicationConfig::getInstance();
        $this->assertThat($application_config->Domain['brandco'], $this->equalTo('brandcotest.com'));
    }

    /**
     * @test
     */
    public function loadYAMLTest() {
        $this->target->loadYAML($this->yml);
        $result =  $this->target->getValues();
        $this->assertThat($result['Domain']['brandco'], $this->equalTo('brandcotest.com'));
    }

    /**
     * @test
     * @expectedException aafwException
     */
    public function loadYAML_Error_Test() {
        $this->target = aafwApplicationConfig::getInstance();
        $this->target->loadYAML('@facebook.hogege');
    }
}
