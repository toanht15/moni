<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class index extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_TOP);

    private $brand_global_setting_service;

    public function validate () {
        return true;
    }

    function doAction() {

        $this->brand_global_setting_service = $this->getService('BrandGlobalSettingService');

        if(!$this->Data['pageStatus']['isLoginAdmin'] && $this->isHideBrandTopPage()){
            return '404';
        }

        $this->setSpecialFanCookie();

        if($this->Data['pageStatus']['isLoginAdmin']) {
            $page_settings_service = $this->createService('BrandPageSettingService');
            $this->Data['pageStatus']['public_flg'] = $page_settings_service->getPageSettingsByBrandId($this->Data['brand']->id)->public_flg;
            $this->Data['pageStatus']['display'] = true;
        }

        $this->Data['pageStatus']['brand_info'] = $this->getFanCountInfo();

        //サードパーティから受け取った値をSessionに保存する
        $this->preUpdateThirdPartyUserRelation();
        if ($this->Data['pageStatus']['userInfo']->id) {
            /** @var $user_service UserService */
            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);
            //セッションに入れたサードパーティの値をDBに保存する
            $this->updateThirdPartyUserRelation($user->id);
        }

        $this->Data['isDisplayFreeArea'] = $this->isDisplayFreeArea();

        return 'user/brandco/index.php';
    }

    private function isHideBrandTopPage(){
        $hide_brand_top_page_setting = $this->brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_BRAND_TOP_PAGE);
        if(!Util::isNullOrEmpty($hide_brand_top_page_setting)){
            return true;
        }
        return false;
    }

    private function isDisplayFreeArea(){
        $can_use_sp_free_area = $this->brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_SP_FREE_AREA);
        if(!Util::isNullOrEmpty($can_use_sp_free_area)){
            return true;
        }

        if(!Util::isSmartPhone()){
            return true;
        }
        
        return false;
    }
}
