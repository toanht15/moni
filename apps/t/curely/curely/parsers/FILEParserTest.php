<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.FILEParser');

class FILEParserTest extends PHPUnit_Framework_TestCase {

    private $target;

    public function setUp() {
        $this->target = new FILEParser();
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->target->getContentType();
        $this->assertThat($result, $this->equalTo('application/octet-stream'));
    }

    /**
     * TODO
     * @test
     */
    public function inTest() {
        $data = '';
        $result = $this->target->in($data);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function getDispositionTest() {
        $data = array(
            'is_attachment' => __ON__,
            'data' => 'file',
        );
        $this->target->out($data);
        $result = $this->target->getDisposition();
        $this->assertNotNull($result);
    }

    /**
     * TODO
     * @test
     */
    public function outTest() {
        $data = array(
            'is_attachment' => __ON__,
            'file_type' => 'file_type',
            'file_name' => 'file_name',
            'data' => 'data',
        );
        $result = $this->target->out($data);
        $this->assertThat($result, $this->equalTo('data'));
    }

    /**
     * TODO
     * @test
     */
    public function out_File_Test() {
        $data = array(
            'is_attachment' => __ON__,
            'data' => 'file',
        );
        $result = $this->target->out($data);
        $this->assertThat($result, $this->equalTo('file'));
    }
}
