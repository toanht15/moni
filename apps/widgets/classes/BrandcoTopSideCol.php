<?php
AAFW::import('jp.aainc.widgets.base.TopSideColBase');

class BrandcoTopSideCol extends TopSideColBase {

    public function doAction($params = array()) {
        $brand_side_menu_service = $this->getService('BrandSideMenuService');

        $sideMenus = $brand_side_menu_service->getDisplayMenuByBrandId($params['brand']->id);
        if($sideMenus) {
            $params['sideMenus'] = $sideMenus->toArray();
        }

        /** @var BrandCmsSettingService $brand_cms_setting_service */
        $brand_cms_setting_service = $this->getService('BrandCmsSettingService');
        $brand_cms_setting = $brand_cms_setting_service->getBrandCmsSettingByBrandId($params['brand']->id);

        if ($brand_cms_setting->category_navi_top_display_flg) {
            /** @var StaticHtmlCategoryService $static_html_tag_service */
            $static_html_tag_service = $this->getService('StaticHtmlCategoryService');
            $params['top_categories'] = $static_html_tag_service->getCategoriesAtDepth(0, $params['brand']->id);
        }

        return $params;
    }

    public function canShowSNSBox() {
        return true;
    }

}