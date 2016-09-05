<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class conversion_setting_form extends BrandcoGETActionBase {
    protected $ContainerName = 'conversion_settings_form';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->createService('ConversionService');
        $this->Data['conversions'] = $conversion_service->getConversionsByBrandId($this->brand->id);

        return 'user/brandco/admin-settings/conversion_setting_form.php';
    }
}
