<?php
AAFW::import('jp.aainc.lib.image.aafwImageDraw');

class aafwImageDrawTest extends BaseTest {

    /** @var aafwImageDraw $target */
    private $target;

    public function setUp() {
        $params = array(
            'Font' => AAFW_DIR . '/t/curely/testfiles/directory/arial.ttf',
            'Color' => 'black',
            'Size' => '15.0',
            //'Text' => 'text'
        );
        $this->target = new aafwImageDraw($params);
    }

    /**
     * @test
     */
    public function getObject_Test() {
        /** @var ImagickDraw $draw */
        $draw = $this->target->getObject();
        $this->assertThat(basename($draw->getfont()), $this->equalTo('arial.ttf'));
        $this->assertThat($draw->getfontsize(), $this->equalTo('15.0'));
    }

}
