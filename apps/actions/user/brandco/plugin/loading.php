<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class loading extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    public function validate() {
        return true;
    }

    public function doAction() {
        return 'user/brandco/plugin/loading.php';
    }
}
