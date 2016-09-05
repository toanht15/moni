<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class create_coupon extends BrandcoGETActionBase {
    protected $ContainerName = 'create_coupon';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        return 'user/brandco/admin-coupon/create_coupon.php';
    }
}
