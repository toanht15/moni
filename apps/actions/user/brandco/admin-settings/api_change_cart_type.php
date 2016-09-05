<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_cart_type extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_change_cart_type';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
        $conversion_service = $this->createService('ConversionService');
        if (!$conversion_service->CART_TYPES[$this->POST['cart_type']]) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        /** @var BrandGlobalSettingService $global_setting_service */
        $global_setting_service = $this->createService('BrandGlobalSettingService');
        $cart_setting = $global_setting_service->getSettingByNameAndBrandId('cart_type', $this->brand->id);
        if (!$cart_setting) {
            $cart_setting = $global_setting_service->createBrandGlobalSetting();
            $cart_setting->name = 'cart_type';
            $cart_setting->brand_id = $this->brand->id;
        }

        $cart_setting->content = $this->POST['cart_type'];

        $global_setting_service->saveGlobalSetting($cart_setting);
        $this->Data['saved'] = 1;
        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
