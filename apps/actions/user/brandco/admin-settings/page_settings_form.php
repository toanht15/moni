<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class page_settings_form extends BrandcoGETActionBase {
    protected $ContainerName = 'page_settings';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var BrandPageSettingService $page_settings_service */
        $page_settings_service = $this->createService('BrandPageSettingService');
        $this->Data['page_settings'] = $page_settings_service->getPageSettingsByBrandId($this->Data['brand']->id);

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $hide_brand_top_page_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_BRAND_TOP_PAGE);
        $this->Data['hide_brand_top_page'] = !Util::isNullOrEmpty($hide_brand_top_page_setting);

		$brand = $this->getBrand();
		$action_form = $brand->toArray();
		$action_form['color_text'] = $brand->getColorText();
		$action_form['favicon_img_url'] = $this->brand->getFaviconUrl();

		$this->assign('ActionForm', array_merge($action_form, $this->Data['page_settings']->toArray()));

        return 'user/brandco/admin-settings/page_settings_form.php';
    }

    function getDefaultTopPageUrl() {
        $top_page_url = '/';
        $top_page_url .= Util::haveDirectoryName($this->getBrand()) ? $this->getBrand()->directory_name . '/' : '';
        $top_page_url .= 'campaigns/10';
        return $top_page_url;
    }
}
