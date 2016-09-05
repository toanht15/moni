<?php
AAFW::import('jp.aainc.aafw.file.aafwFileManager');
AAFW::import('jp.aainc.aafw.file.aafwDirectoryManager');

class aafwDirectoryManagerTest extends BaseTest {

    /** @var aafwDirectoryManager $target */
    private $target;
    private $directory;
    private $recursive_directory;
    private $make_directory;

    public function setUp() {
        $this->target = new aafwDirectoryManager();
        $this->directory = AAFW_DIR . '/t/curely/testfiles/directory/';
        $this->recursive_directory = AAFW_DIR . '/t/curely/testfiles/recursion/fonts/';
        $this->make_directory = AAFW_DIR . '/t/curely/testfiles/dir/';
    }

    /**
     * @test
     */
    public function isDirectoryTest() {
        $result = $this->target->isDirectory($this->directory);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function makeTest() {
        $result = $this->target->make($this->make_directory);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function removeTest() {
        $this->target->make($this->make_directory);
        $this->target->remove($this->make_directory);
        $result = $this->target->isDirectory($this->make_directory);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function getListTest() {
        $result = $this->target->getList($this->directory);
        $this->assertCount(1,$result);
    }

    /**
     * @test
     */
    public function getRecursiveFileListTest() {
        $result = $this->target->getRecursiveFileList($this->recursive_directory);
        $this->assertCount(1,$result);
    }

}
