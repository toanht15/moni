<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.JSParser');

class JSParserTest extends PHPUnit_Framework_TestCase {

    /** @var JSParser parser */
    private $parser;

    public function setUp() {
        $this->parser = new JSParser();
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('application/x-javascript; charset=utf-8'));
    }

    /**
     * @test
     */
    public function inTest() {
        $data = 'abc';
        $result = $this->parser->in($data);
        $this->assertThat($result, $this->equalTo('abc'));
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function outError_Test() {
        $data = '';
        $data = $this->parser->out($data);
    }

    /**
     * @test
     * TODO 返り値の期待が不明
     */
    public function outTest() {
        $data = array(
            '__view__' => 'view',
            '__REQ__' => 'request'
        );
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo(''));
    }
}
