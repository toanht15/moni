<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class edit_static_html_category_form extends BrandcoGETActionBase {
	protected $ContainerName = 'edit_static_html_category_form';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
	public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
        $this->Data['category_id'] = $this->GET['exts'][0];
    }

	public function validate () {
        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->createService('StaticHtmlCategoryService');

        $this->Data['category'] = $static_html_tag_service->getCategoryById($this->Data['category_id']);
        if (!$this->Data['category'] || $this->Data['category']->brand_id != $this->brand->id) {
            return "404";
        }

		return true;
	}

	function doAction() {

        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->createService('StaticHtmlCategoryService');
        $this->Data['categories_tree'] = $static_html_tag_service->getCategoriesTree($this->brand->id);
        $path = $static_html_tag_service->getUrlByCategory($this->Data['category']);
        $position = strrpos($path,'/');
        $this->Data['path'] = substr($path,0, $position).'/';
        $father_category = $static_html_tag_service->getParentOfCategory($this->Data['category_id']);
        $this->Data['father_id'] = $father_category ? $father_category->id : null;

        $category_sns_plugins = $static_html_tag_service->getStaticHtmlCategorySnsPlugins($this->Data['category_id']);
        $sns_plugin_ids = array();

        foreach ($category_sns_plugins as $sns_plugin) {
            $sns_plugin_ids[] = $sns_plugin->sns_plugin_id;
        }
        $this->Data['category']->sns_plugins = $sns_plugin_ids;

        $this->assign('ActionForm', $this->Data['category']->toArray());

		return 'user/brandco/admin-blog/edit_static_html_category_form.php';
	}
}
