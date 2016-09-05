<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_brand_login_setting extends BrandcoPOSTActionBase {
    protected $ContainerName = 'user_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'user_settings_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        return true;
    }

    function doAction() {
        $brand_login_setting_service = $this->getService('BrandLoginSettingService', array($this->getBrand()->id));

        $brand_login_setting_service->updateBrandLoginSettings($this->POST['brand_login_snses']);

        return 'redirect: '.Util::rewriteUrl('admin-settings', 'user_settings_form', array(), array('mid'=>'updated'));
    }
}
