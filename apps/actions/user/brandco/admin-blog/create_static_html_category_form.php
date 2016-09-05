<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class create_static_html_category_form extends BrandcoGETActionBase {
	protected $ContainerName = 'create_static_html_category_form';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
	public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

	public function validate () {
		return true;
	}

	function doAction() {

        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->createService('StaticHtmlCategoryService');
        $this->Data['categories_tree'] = $static_html_tag_service->getCategoriesTree($this->brand->id);

		return 'user/brandco/admin-blog/create_static_html_category_form.php';
	}
}
