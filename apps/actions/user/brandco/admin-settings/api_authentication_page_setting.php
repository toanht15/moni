<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_authentication_page_setting  extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_authentication_page_setting';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        if($this->page_content){
            if(!preg_match('/<a.*?href="##LINKYES##".*?>/',$this->page_content) || !preg_match('/<a.*?href="##LINKNO##".*?>/',$this->page_content)){
                $this->assign('json_data', $this->createAjaxResponse("ng"));
                return false;
            }
        }
        return true;
    }

    function doAction() {
        /** @var BrandPageSettingService $pageSettingsService */
        $pageSettingsService = $this->getService('BrandPageSettingService');
        $pageSettings = $pageSettingsService->getPageSettingsByBrandId($this->brand->id);

        $pageSettings->authentication_page_content = $this->page_content;

        $pageSettingsService->updateBrandPageSetting($pageSettings);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
