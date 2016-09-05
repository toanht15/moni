<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_list extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    protected $ContainerName = 'brand_list';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'brand_list',
    );
	public function validate () {
		return true;
	}

    function doAction() {
        return 'redirect: /brands/index';
    }
}
