<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.CSSParser');

class CSSParserTest extends PHPUnit_Framework_TestCase {

    private $css_parser;

    public function setUp() {
        $this->css_parser = new CSSParser();
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->css_parser->getContentType();
        $this->assertThat($result, $this->equalTo('text/css; charset=utf-8'));
    }

    /**
     * @test
     */
    public function inTest() {
        $data = 'border: double 10px #0000ff;';
        $data = $this->css_parser->in($data);
        $this->assertThat($data, $this->equalTo('border: double 10px #0000ff;'));
    }

    /**
     * @test
     */
    public function outTest() {
        $data = '{}';
        $data = $this->css_parser->out($data);
        $this->assertThat($data, $this->equalTo('{}'));
    }
}
