<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class edit_kpi_groups extends BrandcoManagerPOSTActionBase {

    protected $manager_kpi_service;
    protected $ContainerName = 'edit_kpi_groups';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'edit_kpi_group_form',
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

    public function doThisFirst() {
        $id =$this->id;
        $this->Form['action'] = 'edit_kpi_group_form/'. $id;
    }

    public function validate () {
        return true;
    }

    function doAction() {
        $id =$this->id;
        /** @var ManagerKpiGroupService $kpi_group_service */
        $kpi_group_service = $this->createService('ManagerKpiGroupService');
        $this->Data['kpiGroupColumn'] = array();
        $kpi_group =$kpi_group_service->getKpiGroupById($id);
        $kpi_group->name = $this->POST['name'];
        $kpi_group = $kpi_group_service->updateKpiGroup($kpi_group);

        $kpi_group_service->updateGroupColumnInfo($kpi_group->id, $this->POST['groupColumnIds']);

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl('dashboard', 'list_kpi_groups', array($kpi_group->id), array('mode' => ManagerKpiGroupService::ADD_FINISH ), '', true);
    }
}