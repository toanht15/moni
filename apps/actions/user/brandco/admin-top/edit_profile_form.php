<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class edit_profile_form extends BrandcoGETActionBase {
	protected $ContainerName = 'edit_profile_form';

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	public function validate () {
		return true;
	}

	function doAction() {
		$action_form = array();
		$brand = $this->getBrand();

		$action_form = $brand->toArray();
		$action_form['color_text'] = $brand->getColorText();
		$action_form['favicon_img_url'] = $this->brand->getFaviconUrl();
		$this->assign('ActionForm', $action_form);

		return 'user/brandco/admin-top/edit_profile_form.php';
	}
}