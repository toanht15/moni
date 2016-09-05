<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class create_global_menu_form extends BrandcoGETActionBase {
	protected $ContainerName = 'create_global_menu';

    public $NeedOption = array();
	public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate () {
		return true;
	}

	function doAction() {
		return 'user/brandco/admin-top/create_global_menu_form.php';
	}
}