<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class edit_brand_contract extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'brand_contract';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'brand_contract/{brand_id}',
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'contract_end_date'         => array('required' => true, 'type' => 'str', 'length' => 10),
        'contract_end_time_hh'      => array('required' => true, 'type' => 'num', 'range' => array('<' => 24, '>=' => 0)),
        'contract_end_time_mm'      => array('required' => true, 'type' => 'num', 'range' => array('<' => 60, '>=' => 0)),
        'display_end_date'         => array('required' => true, 'type' => 'str', 'length' => 10),
        'display_end_time_hh'      => array('required' => true, 'type' => 'num', 'range' => array('<' => 24, '>=' => 0)),
        'display_end_time_mm'      => array('required' => true, 'type' => 'num', 'range' => array('<' => 60, '>=' => 0)),
        'closed_title'              => array('required' => true, 'type' => 'str', 'length' => 255),
        'closed_description'        => array('required' => true, 'type' => 'str')
    );

    public function validate() {
        if ($this->Validator->isValid()) {
            $this->Data['contract_end_date'] = $this->POST['contract_end_date'] . ' ' . $this->POST['contract_end_time_hh'] . ':' . $this->POST['contract_end_time_mm'] . ':00';
            $this->Data['display_end_date'] = $this->POST['display_end_date'] . ' ' . $this->POST['display_end_time_hh'] . ':' . $this->POST['display_end_time_mm'] . ':00';
        }

        if (strtotime($this->Data['contract_end_date']) >= strtotime($this->Data['display_end_date'])) {
            $this->Validator->setError('contract_end_date', 'INVALID_DATE');
        }
        return $this->Validator->isValid();
    }

    function doAction() {
        $brand_contract_service = $this->createService('BrandContractService');

        $brand_contract = $brand_contract_service->getBrandContractByBrandId($this->POST['brand_id']);
        if (!$brand_contract) {
            $brand_contract = $brand_contract_service->getEmptyBrandContract();
            $brand_contract->brand_id = $this->POST['brand_id'];
        }

        $brand_contract->contract_end_date = $this->Data['contract_end_date'];
        $brand_contract->display_end_date = $this->Data['display_end_date'];
        $brand_contract->closed_title = $this->POST['closed_title'];
        $brand_contract->closed_description = $this->POST['closed_description'];

        $brand_contract_service->updateBrandContract($brand_contract);

        return 'redirect: ' . Util::rewriteUrl('dashboard', 'brand_contract', array($this->POST['brand_id']), array('mode' => ManagerService::CHANGE_FINISH), '', true);
    }
}