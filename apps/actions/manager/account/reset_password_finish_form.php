<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class reset_password_finish_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'reset_password_finish';
    
    public function validate () {

        return true;
    }

    function doAction() {
        //viewに渡すデータ
        return 'manager/account/reset_password_finish_form.php';
    }
}