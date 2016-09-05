<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.XMLParser');

class XMLParserTest extends PHPUnit_Framework_TestCase {

    /** @var XMLParser $parser */
    private $parser;
    private $file;

    public function setUp() {
        $this->parser = new XMLParser();
        $this->file = file_get_contents(AAFW_DIR . '/t/curely/testfiles/test.xml');
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('text/xml'));
    }

    /**
     * @test
     */
    public function inTest() {
        $result = $this->parser->in($this->file);
        $this->assertThat($result['venture']['company'][0]['name'], $this->equalTo('ソフトバンク株式会社'));
        $this->assertThat($result['venture']['company'][1]['name'], $this->equalTo('楽天株式会社'));
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
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo('<?xml version="1.0" encoding="UTF-8" ?><content>
<a>b</a>
<c>d</c>
<e>f</e>
</content>'));
    }
}
