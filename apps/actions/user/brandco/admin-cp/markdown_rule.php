<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class markdown_rule extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    function validate() {
        return true;
    }

    public function doAction() {
        return 'user/brandco/admin-cp/markdown_rule.php';
    }
}