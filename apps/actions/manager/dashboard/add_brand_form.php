<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class add_brand_form extends BrandcoManagerGETActionBase {

	protected $ContainerName = 'add_brand';

	public $NeedManagerLogin = true;

	public function beforeValidate () {
		$this->resetValidateError();

		if (!$this->getActionContainer('Errors')) {
			$this->Data['mode'] = $this->mode == ManagerService::ADD_FINISH ? ManagerService::ADD_FINISH : $this->mode;
		} else {
			$this->Data['mode'] = ManagerService::ADD_ERROR;
		}
	}

	public function validate () {
		return true;
	}

	function doAction() {
        /** @var ManagerService $manager_service */
        $manager_service = $this->createService('ManagerService');
        /** @var BrandBusinessCategoryService $brand_business_category_service */
        $brand_business_category_service = $this->createService('BrandBusinessCategoryService');
        /** @var BrandContractService $brand_contract_service */
        $brand_contract_service = $this->createService('BrandContractService');
        $managers = $manager_service->getManagers('mail_address');

        //選択可能な契約プランの取得
        $this->Data['plan_list'] = $brand_contract_service->getSelectablePlan();

        foreach($managers as $manager){
            $this->Data['managers'][] = (object)array('id'=> $manager->id, 'name' => $manager->name);
        }
        $array_manager = $this->Data['managers'];
        array_unshift($array_manager, (object)array('id' => 0, 'name' => '指定なし'));
        $this->Data['manager_list'] = array_combine(
            array_map(function($array_manager){return $array_manager->id;}, $array_manager),
            array_map(function($array_manager){return $array_manager->name;}, $array_manager)
        );

		$this->Data['aa_alert_flg'] = $this->Data['ActionError'] ? array($this->Data['ActionForm']['aa_alert_flg'] ? 1 : 0) : array(1);

        $this->Data['brand_business_category_list'] = $brand_business_category_service->getCategoryList();
        array_unshift($this->Data['brand_business_category_list'], '指定なし');

        $this->Data['brand_business_size_list']   = $brand_business_category_service->getSizeList();
        $this->Data['operation_list']             = $brand_contract_service->getOperationList();
        $this->Data['for_production_flg_list']    = $brand_contract_service->getForProductionFlgList();

        return 'manager/dashboard/add_brand_form.php';
	}
}