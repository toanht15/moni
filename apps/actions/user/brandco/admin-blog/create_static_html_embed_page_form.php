<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');

class create_static_html_embed_page_form extends BrandcoGETActionBase {
    protected $ContainerName = 'create_static_html_embed_page';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        if(!$this->canAddEmbedPage()){
            return '404';
        }
        return true;
    }

    function doAction() {

        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $msbcCustomLoginPage = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(),
            BrandGlobalSettingService::MSBC_CUSTOM_LOGIN_PAGE);

        if(Util::isNullOrEmpty($msbcCustomLoginPage)){
            $this->Data['sns_login_types'] = StaticHtmlExternalPageLoginType::$snsLoginTypeOrder;
        }else{
            $this->Data['sns_login_types'] = StaticHtmlExternalPageLoginType::$msbcSnsLoginTypeOrder;
        }

        if(!$this->canLoginByLinkedIn()){
            unset($this->Data['sns_login_types'][SocialAccountService::SOCIAL_MEDIA_LINKEDIN]);
        }

        $action_form['public_flg'] = StaticHtmlEmbedEntry::PUBLIC_PAGE;
        $action_form['public_date'] = $this->getToday();
        $action_form['public_time_hh'] = date('H', time());
        $action_form['public_time_mm'] = date('i', time());

        $this->assign('ActionForm', $action_form);
        return 'user/brandco/admin-blog/create_static_html_embed_page_form.php';
    }
}
