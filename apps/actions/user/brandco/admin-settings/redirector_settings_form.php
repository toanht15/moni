<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class redirector_settings_form extends BrandcoGETActionBase {
    protected $ContainerName = 'redirector_settings_form';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var RedirectorService $redirector_service */
        $redirector_service = $this->createService('RedirectorService');
        $this->Data['redirectors'] = $redirector_service->getRedirectorsByBrandId($this->brand->id);

        return 'user/brandco/admin-settings/redirector_settings_form.php';
    }
}
