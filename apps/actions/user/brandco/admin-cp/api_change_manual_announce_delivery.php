<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');

class api_change_manual_announce_delivery extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_change_manual_announce_delivery';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $CsrfProtect = true;
    public $NeedUserLogin = true;

    function validate() {
        if (!in_array($this->POST['hide_manual'], array(BrandGlobalSettingService::HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL, BrandGlobalSettingService::VIEW_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL))) {
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $brand_global_setting_service->changeAnnounceDeliveryFanListMessageManual($this->getBrand()->id, $this->POST['hide_manual']);
        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
    }
}