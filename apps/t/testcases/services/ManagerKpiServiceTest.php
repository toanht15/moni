<?php

class ManagerKpiServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("ManagerKpiService");
    }

    public function testsetValueByColumnIdAndDate() {
        $columnId = '1';
        $date = '2015-10-20';
        $value = '88';
        $this->target->setValueByColumnIdAndDate($columnId,$date,$value);

        $result = $this->findOne('ManagerKpiValues', array('column_id' => $columnId, 'summed_date' => $date));
        $this->assertEquals(array('column_id' => $columnId, 'summed_date' => $date),
            array('column_id' => $result->column_id, 'summed_date' => $result->summed_date));
    }

    public function testgetGuinness() {
        $columnId = '1';
        $value = '88';
        $this->target->getGuinness($columnId);

        $result = $this->findOne('ManagerKpiValues', array('column_id' => $columnId));
        $this->assertEquals($value, $result->value);
    }
}
