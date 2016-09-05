<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class change_password_form extends BrandcoManagerGETActionBase {

	protected $ContainerName = 'change_password';

	public $NeedManagerLogin = true;

	public function beforeValidate () {
		$this->resetValidateError();

		if (!$this->getActionContainer('Errors')) {
			if ($this->mode == ManagerService::CHANGE_FINISH) {
				$this->Data['mode'] = ManagerService::CHANGE_FINISH;
			} elseif ($this->mode == ManagerService::CHANGE_REQUIRED) {
				$this->Data['mode'] = ManagerService::CHANGE_REQUIRED;
			} else {
				$this->Data['mode'] = '';
			}
		} else {
			$this->Data['mode'] = ManagerService::CHANGE_ERROR;
		}
	}

	public function validate () {
		return true;
	}

	function doAction() {
		return 'manager/dashboard/change_password_form.php';
	}
}