<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class customise_kpi_groups extends BrandcoManagerGETActionBase {
    public $NeedManagerLogin = true;
    private $pageLimited = 20;
    protected $p = 1;

    public function validate () {
        return true;
    }

    function doAction() {
        if ($this->GET['p']) $this->p = $this->GET['p'];

        /** @var ManagerKpiGroupService $manager_kpi_group_service */
        $manager_kpi_group_service = $this->createService('ManagerKpiGroupService');
        /** @var ManagerKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerKpiService');

        $group_id = $this->GET['exts'][0];
        $kpi_group = $manager_kpi_group_service->getKpiGroupById($group_id);
        $this->Data['kpi_group'] = $kpi_group;

        $kpi_group_columns = $manager_kpi_group_service->getKpiGroupColumnsByKpiGroupId($group_id);
        $this->Data['kpi_column_list'] = $kpi_group_columns;

        $this->Data['dates'] = $manager_kpi_service->getDates($this->p, $this->pageLimited);

        // ページング
        $this->Data['totalEntriesCount'] = $manager_kpi_service->getDatesCount();
        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'manager/dashboard/customise_kpi_groups.php';
    }
}
