<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandSocialAccountProfileService');

class api_get_brand_sns_account_profile extends BrandcoGETActionBase
{
    protected $ContainerName = 'api_get_brand_sns_account_profile';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;

    private $brandSocialProfileService;
    private $brandSocialAccountService;
    private $brandSocialAccount;
    private $logger;

    public function doThisFirst(){
        $this->brandSocialAccountService = $this->createService('BrandSocialAccountService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate()
    {
        $this->brand = $this->getBrand();

        if(!$this->brand) {
            $this->logger->error("api_get_brand_sns_account_profile@validate 不正Brand。 brand = null");
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return false;
        }

        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSnsAccountId)) {
            $this->logger->error("api_get_brand_sns_account_profile@validate 不正brandSocialAccountId。brand_id= ".$this->brand->id . "、brand_sns_account_id=".$this->brandSnsAccountId);
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return false;
        }

        $this->brandSocialAccount = $this->brandSocialAccountService->getBrandSocialAccountById($this->brandSnsAccountId);
        $brandSocialAccountProfileServiceFactory = new BrandSocialAccountProfileServiceFactory();

        $this->brandSocialProfileService = $brandSocialAccountProfileServiceFactory->creatBrandSocialAccountProfileService($this->brandSocialAccount->social_app_id);

        if(Util::isNullOrEmpty($this->brandSocialProfileService)){
            $this->logger->error("api_get_brand_sns_account_profile@validate 不正brandSocialAccount。brand_id= ".$this->brand->id . "、brand_sns_account_id=".$this->brandSnsAccountId);
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {

        try {

            $profile = $this->brandSocialProfileService->getProfile($this->brandSocialAccount);
            $this->brandSocialProfileService->updateBrandSocialAccount($this->brandSocialAccount, $profile);

            $json_data = $this->createAjaxResponse("ok", array('profile_image' => $profile['picture_url']));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        } catch (Exception $e) {
            $this->logger->error('Get Brand Social Profile Error');
            $this->logger->error($e);
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
    }
}