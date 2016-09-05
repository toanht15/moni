<?php
require_once dirname(__FILE__) . '/../../../config/define.php';

class AAFWTest extends PHPUnit_Framework_TestCase{

    public function setUp() {
        $_GET['XYZZY'] = 'GET_XYZZY';
        define('XYZZY', 'XYZZY_XYZZY');
    }

    /**
     * @test
     */
    public function testCreateObjectSuperGlobal(){
        $this->assertEquals('GET_XYZZY',AAFW::createObject('$_GET["XYZZY"]'));
        $this->assertEquals('GET_XYZZY',AAFW::createObject("\$_GET['XYZZY']"));
        $this->assertEquals(array('XYZZY'=>'GET_XYZZY'),AAFW::createObject('$_GET'));
    }

    /**
     * @test
     */
    public function testCreateObjectConstant(){
        $this->assertEquals('XYZZY_XYZZY',AAFW::createObject('!XYZZY'));
    }

//    /**
//     * @test
//     */
//    public function testCreateObjectSingleton () {
//        $this->assertInstanceOf('SampleSingleton',AAFW::createObject('-SampleSingleton'));
//        $object = AAFW::createObject('-SampleFactory(SampleClass)');
//        $this->assertInstanceOf('SampleClass',$object);
//        $this->assertInstanceOf('SampleDependency',$object->getDependency());
//    }

}
