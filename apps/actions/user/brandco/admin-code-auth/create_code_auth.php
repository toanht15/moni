<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class create_code_auth extends BrandcoGETActionBase {
    protected $ContainerName = 'create_code_auth';

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        return 'user/brandco/admin-code-auth/create_code_auth.php';
    }
}
