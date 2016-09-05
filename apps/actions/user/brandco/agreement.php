<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class agreement extends BrandcoGETActionBase {

    public $NeedOption = array();

    public function validate () {
        return true;
    }

    function doAction() {
        $brand_page_setting = $this->getBrand()->getBrandPageSetting();
        if (strlen($brand_page_setting->agreement) === 0) {
            return '404';
        }

        $this->Data['agreement'] = $brand_page_setting->agreement;

        return 'user/brandco/agreement.php';
    }
}
