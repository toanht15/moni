<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class api_update_kpi_value extends BrandcoManagerPOSTActionBase {

    protected $AllowContent = array('JSON');

    protected $brand_service;

    protected $ContainerName = 'update_kpi_value';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'kpi',
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'value' => array(
            'required' => 1,
            'type' => 'num',
            'length' => 255,
        ),
        'column_id' => array(
            'required' => 1,
            'type' => 'num',
            'length' => 255,
        ),
        'summed_date' => array(
            'required' => 1,
            'type' => 'date',
        ),
    );

    public function validate () {

        return true;
    }

    function doAction() {
        $brand_service = $this->createService('ManagerKpiService');
        $brand_service->setValueByColumnIdAndDate($this->POST['column_id'], $this->POST['summed_date'], $this->POST['value']);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
