<?php
AAFW::import('jp.aainc.aafw.file.aafwFileManager');
AAFW::import('jp.aainc.aafw.file.aafwDirectoryManager');

class aafwFileManagerTest extends BaseTest {

    /** @var aafwFileManager $target */
    private $target;
    private $file;
    private $write_file;
    private $zip_file;

    public function setUp() {
        $this->target = new aafwFileManager();
        $this->file = AAFW_DIR . '/t/curely/testfiles/test.csv';
        $this->write_file = AAFW_DIR . '/t/curely/testfiles/test.txt';
        $this->zip_file = AAFW_DIR . '/t/curely/testfiles/test.zip';
    }

    /**
     * @test
     */
    public function isFileTest() {
        $return = $this->target->isFile($this->file);
        $this->assertTrue($return);
    }

    /**
     * @test
     */
    public function isFile_No_File_Test() {
        $return = $this->target->isFile('');
        $this->assertFalse($return);
    }

    /**
     * @test
     */
    public function readAllTest() {
        $result = $this->target->readAll($this->file);
        $this->assertThat($result, $this->equalTo('"a","b","c"'));
    }

    /**
     * @test
     */
    public function writeAllTest() {
        $result = $this->target->writeALL($this->write_file, 'write all');
        $this->assertThat($result, $this->equalTo('9'));
    }

    /**
     * @test
     */
    public function createTempFileTest() {
        /** @var aafwTempFileManager $result */
        $result = $this->target->createTempFile();
        $this->assertTrue($result instanceof aafwTempFileManager);
    }

    /**
     * @test
     */
    public function removeTest() {
        $this->target->writeALL($this->write_file, 'write all');
        $this->target->remove($this->write_file);

        $result = $this->target->isFile($this->write_file);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function unzipTest() {
        $result = $this->target->unzip($this->zip_file);
        $this->assertTrue($result instanceof aafwTempFileManager);
    }
}
