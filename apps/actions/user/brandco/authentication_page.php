<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandPageSettingService');

class authentication_page extends BrandcoGETActionBase {

    public $NeedOption = array();

    public $SkipAgeAuthenticate = true;

    private $pageSettings;

    public function beforeValidate () {
        $this->pageSettings = BrandInfoContainer::getInstance()->getBrandPageSetting();;
    }

    public function validate () {

        if (!$this->preview) {

            /** @var BrandGlobalSettingService $brandGlobalSettingService */
            $brandGlobalSettingService = $this->createService('BrandGlobalSettingService');
            $brandGlobalSetting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::AUTHENTICATION_PAGE);

            if(Util::isNullOrEmpty($brandGlobalSetting)){
                return '404';
            }

            if(!$this->pageSettings){
                return '404';
            }

            if(!$this->pageSettings->privacy_required_restricted || !$this->pageSettings->age_authentication_flg){
                return '404';
            }
        }

        return true;
    }

    function doAction() {

        /** @var BrandPageSettingService $pageSettingsService */
        $pageSettingsService = $this->createService('BrandPageSettingService');

        if(!$this->preview || $this->preview == BrandPageSettingService::AUTHENTICATION_PAGE_DEFAULT_PREVIEW_MODE){
            if(!$this->pageSettings->authentication_page_content){

                $parser = new PHPParser();

                $this->Data['page_content'] = $parser->parseTemplate('BrandcoAuthenticationPage.php', array(
                    'restrict_age' => $this->pageSettings->restricted_age,
                ));

            }else{
                $this->Data['page_content'] = $pageSettingsService->evalAgeRestrictMarkdown($this->pageSettings->authentication_page_content,$this->pageSettings->restricted_age);
            }

            if(!$this->preview){
                $this->Data['no_link'] = $this->pageSettings->not_authentication_url;
            }
        } else {

            try {

                $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
                $content = $cache_manager->getCache(CacheManager::AUTHENTICATION_PAGE_PREVIEW_KEY, array($this->brand->id));

                if($content && $content['page_content']){
                    $this->Data['page_content'] = $pageSettingsService->evalAgeRestrictMarkdown($content['page_content'],$this->pageSettings->restricted_age);
                }else{
                    $parser = new PHPParser();
                    $this->Data['page_content'] = $parser->parseTemplate('BrandcoAuthenticationPage.php', array(
                        'restrict_age' => $this->pageSettings->restricted_age,
                    ));
                }
                
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }
        }

        $this->Data['is_preview'] = $this->preview ? 1 : 0;

        return 'user/brandco/authentication_page.php';
    }
}
