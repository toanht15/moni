<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class ManagerBrandKpiColumn extends aafwEntityBase {

    /** @var ManagerBrandKpiService manager_kpi_service */
    protected $manager_kpi_service;

    public function getValue($date, $brandId) {

        if(!$this->manager_kpi_service) {
            $serviceFactory = new aafwServiceFactory ();
            $this->manager_kpi_service = $serviceFactory->create('ManagerBrandKpiService');
        }

        $value = $this->manager_kpi_service->getValue($this->id, $brandId, $date);

        return $value->value !== null ? number_format($value->value) : '-';
    }
}
