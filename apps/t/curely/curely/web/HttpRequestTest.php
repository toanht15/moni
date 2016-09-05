<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.web.HttpRequest');

class HttpRequestTest extends PHPUnit_Framework_TestCase {

    /** @var HttpRequest $target */
    private $target;

    public function setUp() {
        $this->target = new HttpRequest();
    }

    /**
     * @test
     */
    public function getTest() {
        $result = $this->target->get('http://hogehoge/');
        $this->assertThat($result, $this->equalTo(''));
    }

    /**
     * @test
     */
    public function getTest3() {
        $result = $this->target->get('hoge');
        $this->assertFalse($result);
    }
}
