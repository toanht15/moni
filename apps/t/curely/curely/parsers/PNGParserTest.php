<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.PNGParser');

class PNGParserTest extends PHPUnit_Framework_TestCase {

    /** @var PNGParser $parser */
    private $parser;
    private $file;

    public function setUp() {
        $this->parser = new PNGParser();
        $this->file = AAFW_DIR . '/t/curely/testfiles/test.jpg';
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('image/png'));
    }

    /**
     * @test
     */
    public function inTest() {
        $data = 'PNG';
        $result = $this->parser->in($data);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function outTest() {
        $result = $this->parser->out($this->file);
        $this->assertThat($result, $this->equalTo($this->file));
    }
}
