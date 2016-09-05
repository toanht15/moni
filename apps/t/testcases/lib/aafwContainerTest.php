<?php

require_once preg_replace( '#/$#', '', AAFW_DIR ) . '/lib/container/aafwContainer.php';

class aafwContainerTest extends BaseTest {

    public function testTotal01_whenEmpty() {
        $container = new aafwContainer(array());
        $this->assertEquals(0, $container->total());
    }

    public function testTotal02_whenSingle() {
        $container = new aafwContainer(array("TEST"));
        $this->assertEquals(1, $container->total());
    }

    public function testTotal03_whenTwo() {
        $container = new aafwContainer(array("TEST1", "TEST2"));
        $this->assertEquals(2, $container->total());
    }

    public function testToArray01_whenEmpty() {
        $container = new aafwContainer(array());
        $this->assertEquals(array(), $container->toArray());
    }

    public function testToArray02_whenSingle() {
        $container = new aafwContainer(array("TEST"));
        $this->assertEquals(array("TEST"), $container->toArray());
    }

    public function testToArray03_whenTwo() {
        $container = new aafwContainer(array("TEST1", "TEST2"));
        $this->assertEquals(array("TEST1", "TEST2"), $container->toArray());
    }
}