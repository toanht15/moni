<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class add_manager_form extends BrandcoManagerGETActionBase {

	protected $ContainerName = 'add_manager';
	public $ManagerPageId    = Manager::MENU_ADD_BRAND;

	public $NeedManagerLogin = true;

	public function beforeValidate () {
		$this->resetValidateError();

		if (!$this->getActionContainer('Errors')) {
			$this->Data['mode'] = $this->mode == ManagerService::ADD_FINISH ? ManagerService::ADD_FINISH : '';
		} else {
			$this->Data['mode'] = ManagerService::ADD_ERROR;
		}
	}

	public function validate () {
		return true;
	}

	function doAction() {
		return 'manager/dashboard/add_manager_form.php';
	}
}