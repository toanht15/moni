<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class connect_finish extends BrandcoGETActionBase {

    public $NeedOption = array();
    //public $NeedLogin = true;

    public function validate () {
        return true;
    }

    public function doAction() {
        return 'user/brandco/twitter_outer/connect_finish.php';
    }
}
