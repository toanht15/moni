<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class list_kpi_groups extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_KPI_GROUPS;
    private $pageLimited = 20;
    protected $p = 1;

    public function validate() {
        return true;
    }

    function doAction() {
        if ($this->GET['p']) $this->p = $this->GET['p'];

        /** @var ManagerKpiGroupService $manager_kpi_group_service */
        $manager_kpi_group_service = $this->createService('ManagerKpiGroupService');

        $this->Data['kpi_groups'] = $manager_kpi_group_service->getKpiGroups($this->p, $this->pageLimited, null, 'created_at DESC');

        // ページング
        $this->Data['totalEntriesCount'] = $manager_kpi_group_service->countKpiGroups();
        $total_page = floor($this->Data['totalEntriesCount'] / $this->pageLimited) + ($this->Data['totalEntriesCount'] % $this->pageLimited > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'manager/dashboard/list_kpi_groups.php';
    }
}
