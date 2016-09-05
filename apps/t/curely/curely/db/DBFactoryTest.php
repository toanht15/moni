<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import('jp.aainc.aafw.db.DBFactory');

class DBFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * create test
     * @test
     */
    public function getInstanceTest() {
        $db_factory = DBFactory::getInstance();
        $this->assertTrue($db_factory instanceof DBFactory);
    }

    /**
     *
     * @test
     */
    public function getDBTest() {
        $db_factory = DBFactory::getInstance();
        $result = $db_factory->getDB();
        $this->assertTrue($result->Master instanceof DB);
        $this->assertTrue($result->Read instanceof DB);
    }

}
