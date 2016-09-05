<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.ManagerKpiGroupService');
class edit_kpi_group_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'edit_kpi_groups';

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_KPI_GROUPS;

    public function beforeValidate () {
        $this->resetValidateError();

        if ( !$this->getActionContainer('Errors') ) {
            $this->Data['mode'] = $this->mode == ManagerKpiGroupService::ADD_FINISH ? ManagerKpiGroupService::ADD_FINISH : $this->mode;
        } else {
            $this->Data['mode'] = ManagerKpiGroupService::ADD_ERROR;
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {

        $group_id =$this->GET['exts'][0];

        /** @var ManagerKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerKpiService');
        $this->Data['columns'] = $manager_kpi_service->getColumns();

        /** @var ManagerKpiGroupService $manager_kpi_group_service */
        $manager_kpi_group_service = $this->createService('ManagerKpiGroupService');

        $kpi_group = $manager_kpi_group_service->getKpiGroupById($group_id);
        $this->assign('ActionForm', $kpi_group->toArray());
        $this->Data['group_id'] = $group_id;
        $this->Data['kpi_group_columns'] = $manager_kpi_group_service->getKpiGroupColumnsByKpiGroupId($group_id);

        return 'manager/dashboard/edit_kpi_group_form.php';
    }
}
