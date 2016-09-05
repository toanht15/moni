<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class signup extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $isLoginPage = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        return 'redirect: ' . Util::rewriteUrl('my', 'login', array(), $_GET);
    }
}