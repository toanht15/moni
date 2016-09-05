<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class kpi_pager extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'kpi';

    public $NeedManagerLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {
        return 'redirect: ' . Util::rewriteUrl('dashboard', 'kpi', array(), array('limit'=>$this->POST['limit']), '', true);
    }
}