<?php
AAFW::import('jp.aainc.aafw.image.aafwImageFactory');

class aafwImageFactoryTest extends BaseTest {

    private $file;

    public function setUp() {
        $this->file = AAFW_DIR . '/t/curely/testfiles/test.gif';
    }

    /**
     * create test
     * @test
     */
    public function createTest() {
        $image = aafwImageFactory::create($this->file);
        $this->assertTrue($image instanceof aafwImage);
    }

    /**
     * create error test
     * @test
     */
    public function createErrorTest() {
        $image = aafwImageFactory::create($this->file);;
        $this->assertFalse($image instanceof aafwObject);
    }

}
