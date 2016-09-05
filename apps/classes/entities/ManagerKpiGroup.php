<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class ManagerKpiGroup extends aafwEntityBase {

    protected $manager_kpi_group_service;

    public function getColumnCount() {

        $serviceFactory = new aafwServiceFactory ();
        /** @var ManagerKpiGroupService $manager_kpi_group_service */
        $this->manager_kpi_group_service = $serviceFactory->create('ManagerKpiGroupService');

        return $this->manager_kpi_group_service->getKpiGroupColumnCountByKpiGroupId($this->id);
    }
}
