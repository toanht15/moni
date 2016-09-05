<?php
AAFW::import('jp.aainc.aafw.file.aafwTempFileManager');

class aafwTempFileManagerTest extends BaseTest {

    /** @var aafwTempFileManager $target */
    private $target;

    public function setUp() {
        $this->target = new aafwTempFileManager();
    }

    /**
     * @test
     */
    public function getPathTest() {
        $result = $this->target->getPath();
        $this->assertNotNull($result);
    }
}

