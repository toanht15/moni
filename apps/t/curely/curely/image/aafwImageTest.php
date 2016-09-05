<?php
AAFW::import('jp.aainc.lib.image.aafwImage');
AAFW::import('jp.aainc.lib.file.aafwTempFileManager');

/**
 * TODO TempFileManager の挙動がおかしい為これ以上できない
 * Class aafwImageTest
 */
class aafwImageTest extends BaseTest {

    /** @var aafwImage $target */
    private $target;
    private $file;

    public function setUp() {
        $this->target = new aafwImage(new aafwTempFileManager());
        $this->file = AAFW_DIR . '/t/curely/testfiles/test.gif';
    }

    /**
     * @test
     */
    public function getTempFileTest() {
        $result = $this->target->getTempFile();
        $this->assertTrue($result instanceof aafwTempFileManager);
    }
}
