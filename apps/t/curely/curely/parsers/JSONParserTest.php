<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.parsers.JSONParser');

class JSONParserTest extends PHPUnit_Framework_TestCase {

    /** @var JSONParser $parser */
    private $parser;
    private $json;

    public function setUp() {
        $this->parser = new JSONParser();
        $this->json = file_get_contents(AAFW_DIR . '/t/curely/testfiles/test.json');
    }

    /**
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->parser->getContentType();
        $this->assertThat($result, $this->equalTo('application/json; charset=UTF-8'));
    }

    /**
     * @test
     */
    public function inTest() {
        $result = $this->parser->in($this->json);
        $this->assertThat($result['glossary']['title'], $this->equalTo('example glossary'));
    }

    /**
     * @test
     */
    public function outTest() {
        $data = array(
            'json_data' => array(
                'a' => 'b',
                'c' => 'd',
                'e' => 'f'
            )
        );
        $result = $this->parser->out($data);
        $this->assertThat($result, $this->equalTo('{"a":"b","c":"d","e":"f"}'));
    }
}
