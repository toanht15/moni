<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class static_html_categories extends BrandcoGETActionBase {
    protected $ContainerName = 'create_static_html_category_form';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {

        $this->Data['can_use_embed_page'] = $this->canAddEmbedPage();

        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->createService('StaticHtmlCategoryService');
        $this->Data['categories_tree'] = $static_html_tag_service->getCategoriesTree($this->brand->id);

        /** @var BrandCmsSettingService $brand_cms_setting_service */
        $brand_cms_setting_service = $this->createService('BrandCmsSettingService');
        $this->Data['brand_cms_setting'] = $brand_cms_setting_service->getBrandCmsSettingByBrandId($this->brand->id);

        $this->assign('ActionForm', $this->Data['brand_cms_setting']->toArray());

        return 'user/brandco/admin-blog/static_html_categories.php';
    }
}
