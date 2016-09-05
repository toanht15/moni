<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.JPGParser');

class JPGParserTest extends PHPUnit_Framework_TestCase {

    private $parser;
    private $file;

    public function setUp() {
        $this->parser = new JPGParser();
        $this->file = AAFW_DIR . '/t/curely/testfiles/test.jpg';
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('image/jpeg'));
    }

    /**
     * @test
     */
    public function getDisposition_Test() {
        $this->parser->out('');
        $result = $this->parser->getDisposition();
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function getDispositionWithAttachement_Test() {
        $data = array(
            'is_attachment' => true,
            'data' => 'abc',
        );
        $this->parser->out($data);
        $result = $this->parser->getDisposition();
        $this->assertNotNull($result);
    }

    /**
     * @test
     */
    public function inTest() {
        $data = 'JPG';
        $result = $this->parser->in($data);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function out_is_array_data_Test() {
        $data = array(
            'is_attachment' => true,
            'data' => 'abc',
        );
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo('abc'));
    }

    /**
     * @test
     */
    public function out_is_array_file_Test() {
        $data = array(
            'is_attachment' => true,
            'file' => 'abc',
        );
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo('abc'));
    }

    /**
     * @test
     */
    public function out_is_file_Test() {
        $result = $this->parser->out($this->file);
        $this->assertThat($result, $this->equalTo(AAFW_DIR . '/t/curely/testfiles/test.jpg'));
    }
}
