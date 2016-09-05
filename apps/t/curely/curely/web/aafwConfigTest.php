<?php
require_once dirname(__FILE__) . '/../../../../config/define.php';
AAFW::import('jp.aainc.lib.web.aafwConfig');

class aafwConfigTest extends PHPUnit_Framework_TestCase {

    /** @var aafwConfig $target */
    private $target;

    public function setUp() {
        $this->target = new aafwConfig();
    }

    /**
     * parser 読み込み
     * @test
     */
    public function getContentTypeTest() {
        $result = $this->target->getApplicationConfig();
        $this->assertThat($result->Parsers['CSS']['classname'], $this->equalTo('CSSParser'));
        $this->assertThat($result->Parsers['GIF']['classname'], $this->equalTo('GIFParser'));
    }
}
