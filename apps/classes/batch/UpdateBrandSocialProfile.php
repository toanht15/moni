<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.BrandSocialAccountProfileService');

class UpdateBrandSocialProfile extends BrandcoBatchBase{

    private $socialAppId;

    function __construct($argv = null) {
        parent::__construct($argv);

        if($this->argv['social_app_id']) {
            $this->socialAppId = $this->argv['social_app_id'];
        }
    }

    function executeProcess() {
        ini_set('memory_limit', '256M');

        if($this->socialAppId){
            $this->udpateBrandSnsProfileBySnsApp($this->socialAppId);
        } else {

            foreach (SocialApps::$social_pages as $socialAppId) {
                try {
                    $this->udpateBrandSnsProfileBySnsApp($socialAppId);
                } catch (Exception $e) {
                    $this->logger->error('UpdateBrandSocialProfile Error. Social_App_Id = '. $socialAppId);
                    $this->logger->error($e);
                }
            }
        }
    }
    
    private function udpateBrandSnsProfileBySnsApp($socialAppId){

        $brandSocialAccountProfileServiceFactory = new BrandSocialAccountProfileServiceFactory();
        $brandSocialAccountProfileService = $brandSocialAccountProfileServiceFactory->creatBrandSocialAccountProfileService($socialAppId);

        if(Util::isNullOrEmpty($brandSocialAccountProfileService)){
            $this->logger->error('UpdateBrandSocialProfile: 正しいSocial_App_Idを入力してください。');
            exit;
        }

        $brandSocialAccountService = $this->service_factory->create('BrandSocialAccountService');
        $socialAccounts = $brandSocialAccountService->getBrandsSocialAccountsBySocialAppIdAndExpiredFlg($socialAppId);
        if(Util::isNullOrEmpty($socialAccounts)){

            return;
        }

        foreach($socialAccounts as $socialAccount){

            try{
                $profile = $brandSocialAccountProfileService->getProfile($socialAccount);
                $brandSocialAccountProfileService->updateBrandSocialAccount($socialAccount, $profile);
            } catch(Exception $e){
                $this->logger->error('UpdateBrandSocialProfile Error: Brand_Social_Account_ID: '. $socialAccount->id);
                $this->logger->error($e);
            }

        };

    }
}