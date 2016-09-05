<?php
AAFW::import ('jp.aainc.classes.services.ManualService');

class ManualServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('ManualService');
    }

    public function testGetManuals01_正常系() {
        $this->truncateAll('Manuals');
        $this->entity('Manuals', array('title' => 'hoge', 'url' => 'http://hoge.jp', 'type' => 0));
        $this->entity('Manuals', array('title' => 'huga', 'url' => 'http://huga.jp', 'type' => 1));

        $actual = $this->target->getAllManuals(array('order' => 'order_num ASC'));

        $this->assertEquals(2, count($actual->toArray()));
    }

    public function testGetManuals02_異常系() {
        $this->truncateAll('Manuals');

        $actual = $this->target->getAllManuals(array('order' => 'order_num ASC'));

        $this->assertEquals(array(), $actual);
    }
}