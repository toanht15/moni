<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');

class api_change_hide_manual extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_change_hide_manual';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $CsrfProtect = true;
    public $NeedUserLogin = true;

    public function validate() {
        if (in_array($this->POST['hide_manual'],
                array(BrandGlobalSettingService::VIEW_FAN_LIST_MESSAGE_MANUAL, 
                    BrandGlobalSettingService::HIDE_FAN_LIST_MESSAGE_MANUAL)) == false) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        
        $brand = $this->getBrand();

        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        if( $brand_global_setting_service->changeHideFanListMessageManual($brand->id, $this->POST['hide_manual']) ){
            $json_data = $this->createAjaxResponse("ok");
        }else{
            $json_data = $this->createAjaxResponse("ng");
        }
        
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
