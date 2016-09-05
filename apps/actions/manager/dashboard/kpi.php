<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class kpi extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_KPI;
    protected $p = 1;
    public function validate () {
        return true;
    }

    function doAction() {
        if($this->GET['p']) $this->p = $this->GET['p'];
        if (!$this->limit || !$this->isNumeric($this->limit)) $this->limit = 20;

        /** @var ManagerKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerKpiService');

        // 表示日付
        $this->Data['dates'] = $manager_kpi_service->getDates($this->p, $this->limit);
        $this->Data['manager_kpi_columns'] = $manager_kpi_service->getColumns();
        // ギネス記録取得
        $this->Data['guinnesses'] = $manager_kpi_service->getGuinness($this->Data['manager_kpi_columns']);

        // ページング
        $this->Data['totalEntriesCount'] = $manager_kpi_service->getDatesCount();
        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->limit ) + ( $this->Data['totalEntriesCount'] % $this->limit > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        return 'manager/dashboard/kpi.php';
    }
}
