<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class add_kpi_groups extends BrandcoManagerPOSTActionBase {

    protected $manager_kpi_service;
    protected $ContainerName = 'add_kpi_group';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'add_kpi_group_form',
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        ),
    );

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var ManagerKpiGroupService $kpi_group_service */
        $kpi_group_service = $this->createService('ManagerKpiGroupService');
        $this->Data['kpiGroupColumn'] = array();
        $kpi_group = $kpi_group_service->createKpiGroups($this->POST);

        foreach($this->POST["groupColumnIds"] as $group_column_id) {
            $group_column = $kpi_group_service->createEmptyKpiGroupColumns();
            $group_column ->manager_kpi_column_id = $group_column_id;
            $group_column ->manager_kpi_group_id = $kpi_group->id;
            $kpi_group_service->saveGroupColumn($group_column);
        }

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl('dashboard', 'list_kpi_groups', array(), array('mode' => ManagerKpiGroupService::ADD_FINISH ), '', true);
    }
}