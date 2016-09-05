<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.ManagerKpiGroupService');
class add_kpi_group_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'add_kpi_group';

    public $NeedManagerLogin = true;

    public function beforeValidate () {
        $this->resetValidateError();

        if ($this->getActionContainer('Errors')) {
            $this->Data['mode'] = ManagerKpiGroupService::ADD_ERROR;
        } else {
            $this->Data['mode'] = ($this->mode == ManagerKpiGroupService::ADD_FINISH) ? ManagerKpiGroupService::ADD_FINISH : $this->mode;
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var ManagerKpiService $manager_kpi_service */
        $manager_kpi_service = $this->createService('ManagerKpiService');
        $this->Data['columns'] = $manager_kpi_service->getColumns();

        return 'manager/dashboard/add_kpi_group_form.php';
    }
}
