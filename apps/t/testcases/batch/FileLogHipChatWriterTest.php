<?php
AAFW::import('jp.aainc.classes.batch.FileLogHipChatWriter');

class FileLogHipChatWriterTest extends BaseTest {

    public function testDoProcess01_success() {
        $fp = fopen("/tmp/test.txt", "w");
        fputs($fp, "TESTほげら！");
        fclose($fp);
        chmod("/tmp/test.txt", 777);

        $target = new FileLogHipChatWriter();

        $result = $target->doProcess(array("FileLogHipChatWriter.php", "/tmp/test.txt"));
        $this->assertEquals(FileLogHipChatWriter::RESULT_SUCCESS, $result);
    }

    public function testDoProcess02_noArg() {
        $target = new FileLogHipChatWriter();
        $result = $target->doProcess(array());
        $this->assertEquals(FileLogHipChatWriter::RESULT_FAILURE, $result);
    }

    public function testDoProcess03_notExist() {
        $target = new FileLogHipChatWriter();
        $result = $target->doProcess(array('/tmp/NOT_EXISTS'));
        $this->assertEquals(FileLogHipChatWriter::RESULT_FAILURE, $result);
    }
}