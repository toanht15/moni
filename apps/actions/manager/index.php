<?php
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

require_once 'base/aafwActionBase.php';

class index extends BrandcoManagerGETActionBase {

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: /brands/index';
    }
}