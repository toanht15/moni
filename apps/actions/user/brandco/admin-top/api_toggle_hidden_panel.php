<?php
AAFW::import ( 'jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase' );
AAFW::import ( 'jp.aainc.classes.entities.LinkEntry' );
AAFW::import('jp.aainc.classes.CacheManager');

class api_toggle_hidden_panel extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_toggle_hidden_panel';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function beforeValidate() {
        $this->Data ['brandSocialAccountId'] = $this->POST ['brandSocialAccountId'];
        $this->Data ['entryId'] = $this->POST ['entryId'];
    }

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $service = $this->createService ( 'BrandSocialAccountService' );

        if($this->Data ['brandSocialAccountId']){
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->Data['brand']->id);
            if(!$idValidator->isCorrectEntryId($this->Data ['brandSocialAccountId'])) return false;

            $stream = $service->getStreamByBrandSocialAccountId($this->Data ['brandSocialAccountId']);
            $this->Data['service'] = $this->createService ( get_class ( $stream) . 'Service' );

            $idValidator = new StreamValidator(get_class ( $stream) . 'Service', $this->Data['brand']->id);
            if(!$idValidator->isCorrectEntryId($this->Data ['entryId'])) return false;

        }elseif($this->service_prefix){
            if (BrandcoValidatorBase::isStreamValidatorBasedService($this->service_prefix . 'Service')) {
                $idValidator = new StreamValidator($this->service_prefix.'Service', $this->Data['brand']->id);
            } elseif ($this->service_prefix.'Service' == BrandcoValidatorBase::SERVICE_NAME_LINK) {
                $idValidator = new StaticEntryValidator($this->service_prefix . 'Service', $this->Data['brand']->id);
            } else {
                return false;
            }

            if (!$idValidator->isCorrectEntryId($this->Data ['entryId'])) {
                return false;
            }

            $this->Data['service'] = $this->createService($this->service_prefix . 'Service' );

        } else {
            return false;
        }

        return true;
    }

    function doAction() {

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->Data['brand']->id);

        // Redisの操作
        $entry = $this->Data['service']->getEntryById($this->Data ['entryId']);
        $panel_service = $entry->priority_flg ? $this->createService("TopPanelService") : $this->createService("NormalPanelService");

        if ($this->service_prefix . 'Service' == BrandcoValidatorBase::SERVICE_NAME_PAGE) {

            if ($entry->top_hidden_flg == 0) {
                $entry->top_hidden_flg = 1;
                $entry->priority_flg = 0;
                $entry->manual_off_flg = 1;

                if (!$entry->isPrePublicPage()) {
                    $panel_service->deleteEntry($this->Data['brand'], $entry);
                }
            } else {
                $entry->top_hidden_flg = 0;
                $entry->manual_off_flg = 0;

                if (!$entry->isPrePublicPage()) {
                    $panel_service->addEntry($this->Data['brand'], $entry);
                }
            }
            $this->Data['service']->updateEntry($entry);
        } else {

            // Auto approving photo user while adding photo panel to TOP
            if ($this->service_prefix . 'Service' == BrandcoValidatorBase::SERVICE_NAME_PHOTO && $entry->hidden_flg != 0) {
                $photo_user = $entry->getPhotoUser();

                if (!$photo_user->approval_status != PhotoUser::APPROVAL_STATUS_APPROVE) {
                    $photo_user_service = $this->createService('PhotoUserService');

                    $photo_user->approval_status = PhotoUser::APPROVAL_STATUS_APPROVE;
                    $photo_user_service->updatePhotoUser($photo_user);
                }
            }

            if ($entry->hidden_flg == 0) {
                $panel_service->deleteEntry($this->Data['brand'], $entry);
            } else {
                $panel_service->addEntry($this->Data['brand'], $entry);
            }
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}