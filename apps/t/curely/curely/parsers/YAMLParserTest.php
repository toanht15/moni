<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.YMLParser');

class YAMLParserTest extends PHPUnit_Framework_TestCase {

    private $parser;

    public function setUp() {
        $this->parser = new YAMLParser();
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('text/yaml'));
    }

    /**
     * @test
     */
    public function inTest() {
        $data = "--
hoge:
 - bar
foo:
 bar: 1";
        $data = $this->parser->in($data);
        $this->assertFalse($data[0]);
        $this->assertThat($data['hoge'][0], $this->equalTo('bar'));
        $this->assertThat($data['foo']['bar'], $this->equalTo('1'));
    }

    /**
     * @test
     */
    public function outTest() {
        $data = array(
            'a' => 'b',
            'c' => 'd',
            'e' => 'f'
        );
        $data = $this->parser->out($data);
        $this->assertThat($data, $this->equalTo('---
a: b
c: d
e: f
'
));
    }
}
