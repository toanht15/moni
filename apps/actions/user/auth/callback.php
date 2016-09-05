<?php
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class callback extends aafwGETActionBase {

	public function validate () {
		if($this->shouldHaveDirectoryName() && !$this->getSession('directoryName')) return false;
		return true;
	}

	function doAction() {

		if($this->getSession('nativeLoginFlg') || $this->getSession('mailLoginFlg')){
			$this->setSession('nativeLoginFlg',0);
			$this->setSession('mailLoginFlg',0);
			if($this->getSession('authRedirectUrl'))return 'redirect: ' . $this->getSession('authRedirectUrl');
		}

		$redirectUrl = Util::getMappedServerName();
		if ($this->shouldHaveDirectoryName()) {
			$redirectUrl .= '/' . $this->getSession('directoryName');
		}
		$redirectUrl .= '/auth/signup' . '?code=' . $this->GET['code'];
        if ($this->GET['state']) {
            $redirectUrl .= '&state=' . $this->GET['state'];
        }

		return 'redirect: ' . config('Protocol.Secure') .'://' . $redirectUrl;
	}


	private function shouldHaveDirectoryName() {
		$mapped_brand_id = Util::getMappedBrandId();
		if ($mapped_brand_id !== Util::NOT_MAPPED_BRAND) {
			$brand_service = $this->createService('BrandService');
			$brand = $brand_service->getBrandById($mapped_brand_id);
			$mapped_server_name = Util::getMappedServerName($brand->id);
			if ($brand->directory_name === $mapped_server_name) {
				return false;
			}
		}

		return true;
	}
}

