<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class authentication_page_preview extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        $this->Data['preview_url'] = base64_decode($this->preview_url);
        $this->Data['brand'] = $this->getBrand();

        return 'user/brandco/authentication_page_preview.php';
    }
}
