<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class index extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;

	public function validate () {
		return true;
	}

    function doAction() {
        return 'redirect: /brands/index';
    }
}