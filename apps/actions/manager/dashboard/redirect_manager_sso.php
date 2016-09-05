<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class redirect_manager_sso extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;

	public function validate () {
		return true;
	}

    function doAction() {

        return 'redirect: ' . $this->GET['redirect_uri'] . '?' . ManagerService::generateOnetimeToken($this->getSession('managerUserId'));
    }
}
