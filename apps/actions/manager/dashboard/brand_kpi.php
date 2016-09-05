<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_kpi extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    private $pageLimited = 20;
    protected $p = 1;

    public function validate () {
        return true;
    }

    function doAction() {
        if($this->GET['p']) $this->p = $this->GET['p'];
        $this->Data['brandId'] = $this->GET['exts'][0];

        /** @var ManagerBrandKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerBrandKpiService');
        $brand_service = $this->createService('BrandService');
        $this->Data['brand_name'] = $brand_service->getBrandById($this->Data['brandId'])->name;
        $this->Data['columns'] = $manager_kpi_service->getColumns();
        $this->Data['dates'] = $manager_kpi_service->getDates($this->p, $this->pageLimited);
        $this->Data['records'] = $manager_kpi_service->getBrandKpiGuinness($this->Data['columns'], $this->Data['brandId']);

        // ページング
        $this->Data['pageLimited'] = $this->pageLimited;
        $this->Data['totalEntriesCount'] = $manager_kpi_service->getDatesCount();

        return 'manager/dashboard/brand_kpi.php';
    }
}
