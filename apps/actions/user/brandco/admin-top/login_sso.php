<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class login_sso extends BrandcoGETActionBase {

    public $NeedOption = array();
	public function validate () {
		return true;
	}

	function doAction() {
		$enterprise_service = $this->createService('Monipla2EnterpriseService');
		$enterprise = $enterprise_service->getEnterpriseById($this->getBrand()->enterprise_id);

		$enterprise_sso_token_service = $this->createService('Monipla2EnterpriseSSOTokenService');
		$enterprise_sso_token = $enterprise_sso_token_service->createEnterpriseSSOToken($enterprise);

		$redirect_uri = urlencode('https://' . Util::getMappedServerName() . '/');
		return 'redirect: ' . Util::rewriteUrl('account', 'login_sso', null, array('token' => $enterprise_sso_token->token, 'redirect_uri' => $redirect_uri), 'http://' . aafwApplicationConfig::getInstance()->query('Domain.admin-top') . '/');
	}
}