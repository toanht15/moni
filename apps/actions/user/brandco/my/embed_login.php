<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class embed_login extends BrandcoGETActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'embed_logging';
    public $isLoginPage = true;

    private $pageUrl;
    private $page;

    public function validate() {

        if(!$this->canAddEmbedPage()){
            return '404';
        }

        $this->pageUrl = $this->GET['page_url'];

        if(!$this->pageUrl){
            return '404';
        }
        
        $staticHtmlEntryService = $this->createService('StaticHtmlEntryService');
        $this->page = $staticHtmlEntryService->getEntryByBrandIdAndPageUrl($this->brand->id,$this->pageUrl);

        if(!$this->page){
            return '404';
        }

        return true;
    }

    public function doAction() {

        $this->Data['staticHtmlEntry'] = $this->page;
        $this->Data['pageUrl'] = $this->pageUrl;

        if (!$this->isLogin()) {

            $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
            $msbcCustomLoginPage = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(),
                BrandGlobalSettingService::MSBC_CUSTOM_LOGIN_PAGE);

            $this->Data['msbc_custom_login_page'] = !Util::isNullOrEmpty($msbcCustomLoginPage);

            $this->Data['login_types'] = $this->getLoginType();
            return 'user/brandco/my/embed_logging.php';
        }
        
        // 変数に値を格納する
        $login_redirect_url = $this->getSession('loginRedirectUrl');

        // Auto redirect to NeedRedirect page
        if ($login_redirect_url) {
            return 'redirect: ' . $login_redirect_url;
        }

        return 'user/brandco/embed_error_page.php';
    }

    public function getLoginRedirectUrl() {
        $login_redirect_url = $this->getSession('loginRedirectUrl');
        if (!$login_redirect_url) {
            return $login_redirect_url;
        }

        $parsed_url = parse_url($login_redirect_url);

        $mapped_brand_id = Util::getMappedBrandId($parsed_url['host']);
        if ($mapped_brand_id != Util::NOT_MAPPED_BRAND) {
            if ($this->getBrand()->id != $mapped_brand_id) {
                return null;
            }
        } else {
            $parsed_request_uri = Util::parseRequestUri($parsed_url['path']);
            if ($parsed_request_uri['directory_name'] && $parsed_request_uri['directory_name'] != $this->getBrand()->directory_name) {
                return null;
            }
        }

        return $login_redirect_url;
    }

    private function getLoginType(){
        $snsLoginTypes = array();

        if($this->page){
            $staticHtmlLoginTypes = $this->page->getStaticHtmlExternalPageLoginTypes();
            foreach($staticHtmlLoginTypes as $staticHtmlLoginType){
                $snsLoginTypes[] = $staticHtmlLoginType->social_media_id;
            }
        }

        return $snsLoginTypes;
    }
}
