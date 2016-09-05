<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class logout extends BrandcoGETActionBase {

    public $NeedOption = array();
	private $redirect_url = '';

	public function validate () {
		return true;
	}

	function doAction() {
		if( !$this->isLogin() ) {
			return 'redirect: https://' . $this->redirect_url;
		}

		if ($this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('logoutRedirectUrl')) {
			$redirect_url = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('logoutRedirectUrl');
			$this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('logoutRedirectUrl', null);

			return 'redirect: ' . $redirect_url;
		}

		return 'redirect: ' . Util::rewriteUrl ( '', '' );
	}

	function doThisLast() {
		$brands_users_relation_service = $this->createService('BrandsUsersRelationService');
		$brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($this->brand->id, $this->getSession('pl_monipla_userId'));

		$this->setLogout($brands_users_relation);

		$this->resetActionContainerByType();
	}
}

