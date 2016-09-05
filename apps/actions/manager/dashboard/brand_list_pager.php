<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class brand_list_pager extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'brand_list';

    public $NeedManagerLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: ' . Util::rewriteUrl('dashboard', 'brand_list', array(), array('limit'=>$this->POST['limit'], 'test_page'=>$this->POST['test_page']), '', true);
    }
}