<?php
require_once __DIR__ . '/../../../config/define.php';

class aafwAutoloaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function AutoloadTest2() {
        $target = new Util();
        $this->assertTrue($target instanceof Util);
    }

//    /**
//     * @test
//     * @expectedException Exception
//     */
//    public function AutoLoadErrorTest() {
//        $target = new SampleLibrary();
//        //$this->assertTrue($target instanceof SampleLibrary);
//    }
}
