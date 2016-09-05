<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class change_password extends BrandcoManagerPOSTActionBase {

	protected $ContainerName = 'change_password';
	protected $Form = array(
		'package' => 'dashboard',
		'action' => 'change_password_form',
	);
	public $NeedManagerLogin = true;
	public $ManagerPageId    = Manager::MENU_CHANGE_PASSWORD;
	public $CsrfProtect = true;

	protected $ValidatorDefinition = array(
		'old_password' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 32,
			'validator' => array('AlnumSymbol')
		),
		'new_password' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 32,
			'validator' => array('AlnumSymbol')
		),
		'new_password_confirm' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 32,
			'validator' => array('AlnumSymbol')
		),
	);

	public function validate () {

		if(md5($this->old_password) != $this->manager->password) {
			$this->Validator->setError('old_password', 'NOT_COLLECT_OLDPASSWORD');
		}

		if(md5($this->new_password) == $this->manager->password) {
			$this->Validator->setError('new_password', 'SAME_NEW_AND_OLD');
		}

		if($this->new_password_confirm != $this->new_password) {
			$this->Validator->setError('new_password_confirm', 'NOT_COLLECT_NEWPASSWORD');
		}
		return !$this->Validator->getErrorCount();
	}

	public function doAction() {

		// パスワード変更処理
		$manager_service = $this->createService('ManagerService');
		$manager_service->changeManagerPass($this->manager, md5($this->new_password));

		return 'redirect: ' . Util::rewriteUrl('dashboard', 'change_password_form', array(), array('mode' => ManagerService::CHANGE_FINISH ), '', true);
	}
}