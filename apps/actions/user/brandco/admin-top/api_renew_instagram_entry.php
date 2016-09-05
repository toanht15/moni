<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');
AAFW::import('jp.aainc.classes.CacheManager');

class api_renew_instagram_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_renew_instagram_entry';
    protected $AllowContent = array('JSON');

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function beforeValidate () {
        $this->Data['service'] = $this->createService('InstagramStreamService');
    }

    public function validate () {
        $this->brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) return false;

        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_INSTAGRAM, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) return false;

        return true;
    }

    function doAction() {
        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->brand->id);

        $logger = aafwLog4phpLogger::getDefaultLogger();

        /** @var BrandSocialAccountService $service */
        $service = $this->createService('BrandSocialAccountService');
        $brand_social_account = $service->getBrandSocialAccountById($this->brandSocialAccountId);

        $entry = $this->Data['service']->getEntryById($this->entryId);

        try {
            $instagram = new Instagram();
            $response = $instagram->getMediaInfo($entry->object_id, $brand_social_account->token);

            if (!$response || $err_mess = $service->getErrorMessage($brand_social_account, $response)) {
                $logger->error('api_renew_instagram_entry@doAction err_message = ' . $err_mess . ' $brand_social_account_id='.$brand_social_account->id);
                $json_data = $this->createAjaxResponse('ng', array(), array('message' => $err_mess));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }
        } catch (Exception $e) {
            $logger->error("api_renew_instagram_entry#doAction() Exception media_id = " . $entry->object_id);
            $logger->error($e);

            $json_data = $this->createAjaxResponse(Instagram::EXCEPTION_ACCESS_DENIED);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        try {
            $this->Data['service']->renewEntry($entry, $response);
        } catch (Exception $e) {
            $logger->error("api_renew_instagram_entry#doAction() error" . "entry_id=" . $entry->id);
            $logger->error($e);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}