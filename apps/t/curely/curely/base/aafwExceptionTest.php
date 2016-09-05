<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwException');

class aafwExceptionTest extends PHPUnit_Framework_TestCase {

    private $target;

    /**
     * @test
     */
    public function getAppErrorCodeTest() {
        $this->target = new aafwException();
        $result = $this->target->getAppErrorCode();
        $this->assertThat($result, $this->equalTo('ERROR_BASE_0001'));
    }

    /**
     * @test
     */
    public function getAppErrorCode2Test() {
        $this->target = new aafwException('ApplicationError', 'ERROR_ARGS');
        $result = $this->target->getAppErrorCode();
        $this->assertThat($result, $this->equalTo('ERROR_ARGS'));
    }
}
