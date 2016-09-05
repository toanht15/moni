<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class index extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

	public function validate () {
		return true;
	}

	function doAction() {
		return 'user/brandco/admin-msg/index.php';
	}
}