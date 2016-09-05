<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_csv extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;

    protected $ContainerName = 'brand_csv';
    private $pageLimited = 20;
    protected $p = 1;

    public function validate () {
        return true;
    }

    function doAction() {
        if($this->GET['p']) $this->p = $this->GET['p'];
        $this->Data['brandId'] = $this->GET['exts'][0];

        $brand_service = $this->createService('BrandService');
        $this->Data['brand'] = $brand_service->getBrandById($this->Data['brandId']);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->Data['cps'] = $cp_flow_service->getCpsNotDraftByBrandId($this->Data['brand']->id);

        return 'manager/dashboard/brand_csv.php';
    }
}
