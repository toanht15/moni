<?php
AAFW::import ('jp.aainc.classes.services.ManagerKpiGroupService');

class ManagerKpiGroupServiceTest extends BaseTest {

    public function setUp() {
        $this->target = aafwServiceFactory::create("ManagerKpiGroupService");
    }

    public function testManagerGroupKpiSave(){
        $name = array("name"=>"saved");
        $this->target->createKpiGroups($name);
        $result = $this->findOne('ManagerKpiGroups', array('name' => $name));
        $this->assertEquals($name['name'], $result->name);
    }

    public function testManagerGroupKpiColumnSave(){
        $ManagerKpiGroup = $this->entity('ManagerKpiGroups',array('name' => "save"));
        $kpiColumnIds = "1";
        $column = $this->target->createEmptyKpiGroupColumns();
        $column->manager_kpi_column_id = $kpiColumnIds;
        $column ->manager_kpi_group_id = $ManagerKpiGroup->id;
        $this->target->saveGroupColumn($column);

        $result = $this->findOne('ManagerKpiGroupColumns', array('manager_kpi_group_id' => $ManagerKpiGroup->id));
        $this->assertEquals($ManagerKpiGroup->id, $result->manager_kpi_group_id);
    }

    public function testManagerGroupKpiCount() {
        $ManagerKpiGroup = $this->entity('ManagerKpiGroups',array('name' => "saving"));
        $kpiColumnIds = "1";
        $column = $this->target->createEmptyKpiGroupColumns();
        $column->manager_kpi_column_id = $kpiColumnIds;
        $column ->manager_kpi_group_id = $ManagerKpiGroup->id;
        $this->target->saveGroupColumn($column);

        $count = $this->target->getKpiGroupColumnCountByKpiGroupId($ManagerKpiGroup->id);
        $this->assertEquals(1, $count);
    }

    public function testManagerKpiGroupServiceTestCount() {
        $managerKpiGroups =  $this->target->getKpiGroups();
        $count = $this->countEntities('ManagerKpiGroups',array($managerKpiGroups));
        $val = $this->target->countKpiGroups();
        $this->assertEquals($count ,$val);
    }


}
