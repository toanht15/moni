<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class select_panel_kind extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	public function validate () {

		return true;
	}

	function doAction() {
		$brand_social_account_service = $this->createService('BrandSocialAccountService');
		$rss_service = $this->createService('RssStreamService');
		$brand = $this->getBrand();

		$this->Data['rssPanelKinds'] = $rss_service->getStreamByBrandId($brand->id);
		$this->Data['socialPanelKinds'] = $brand_social_account_service->getBrandSocialAccountByBrandId($brand->id);

		return 'user/brandco/admin-top/select_panel_kind.php';
	}
}