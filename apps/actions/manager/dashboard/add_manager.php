<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class add_manager extends BrandcoManagerPOSTActionBase {

	protected $checkMatchManager;
	protected $ContainerName = 'add_manager';
	protected $Form = array(
		'package' => 'dashboard',
		'action' => 'add_manager_form',
	);

	public $NeedManagerLogin = true;
	public $CsrfProtect = true;

	protected $ValidatorDefinition = array(
		'username' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 255,
			'validator' => array('NotAlnumSymbol')
		),
		'email' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 255,
			'validator' => array('MailAddress')
		),
		'password' => array(
			'required' => 1,
			'type' => 'str',
			'length' => 32,
			'validator' => array('AlnumSymbol')
		),
	);

	public function validate () {
		// メールアドレスの重複チェック
		$this->checkMatchManager = $this->createService('ManagerService');
		$matchManager = $this->checkMatchManager->getManagerAccount($this->email);
		if ($matchManager) {
			$this->Validator->setError('email', 'EXISTED_MAIL_ADDRESS');
		}

		return !$this->Validator->getErrorCount();
	}

	function doAction() {

		// 管理者追加処理
		$manager_service = $this->createService('ManagerService');
		$manager_service->setManager($this->POST);

		return 'redirect: ' . Util::rewriteUrl('dashboard', 'add_manager_form', array(), array('mode' => ManagerService::ADD_FINISH ), '', true);
	}
}