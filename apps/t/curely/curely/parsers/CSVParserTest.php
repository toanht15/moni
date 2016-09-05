<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class CSVParserTest extends PHPUnit_Framework_TestCase {

    /** @var CSVParser $parser */
    private $parser;

    public function setUp() {
        $this->parser = new CSVParser();
    }

    /**
     * @test
     */
    public function getContentType_Test() {
        $this->assertThat($this->parser->getContentType(), $this->equalTo('application/x-csv'));
    }

    /**
     * @test
     */
    public function in_Test() {
        $result = $this->parser->in(AAFW_DIR . '/t/curely/testfiles/test.csv');
        $this->assertThat($result[0][0], $this->equalTo('a'));
        $this->assertThat($result[0][1], $this->equalTo('b'));
        $this->assertThat($result[0][2], $this->equalTo('c'));
    }

    /**
     * @test
     */
    public function out_Test() {
        $data = array(
            'header' => array('a','b','c'),
            'list' => array(
                array('aaa','aaa','aaa'),
                array('bbb','bbb','bbb')
            ),
            'delimiter' => ','
        );
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo('"a","b","c"
"aaa","aaa","aaa"
"bbb","bbb","bbb"
'));
    }
}
