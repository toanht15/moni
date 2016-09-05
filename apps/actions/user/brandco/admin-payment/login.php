<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class login extends BrandcoGETActionBase{

    public $NeedOption = array();

    function validate() {
        return true;
    }

    function doAction() {
        $this->setSession('loginRedirectUrl', Util::rewriteUrl('admin-payment', 'order_list',array($this->GET['exts'][0])));
        return "redirect: ".Util::rewriteUrl("my","login");
    }
}
