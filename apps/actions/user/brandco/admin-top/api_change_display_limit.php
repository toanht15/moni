<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_change_display_limit extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_change_stream_hidden_flg';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate() {
        $brand = $this->getBrand();
        if (!$this->isNumeric($this->value)) {
            return false;
        }

        if ($this->brandSocialAccountId) {
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $brand->id);
            if (!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) return false;
        } elseif ($this->stream_prefix) {
            $idValidator = new StreamValidator($this->stream_prefix . 'Service', $brand->id);
            if (!$idValidator->isPanelServiceName($this->stream_prefix . 'Service')) return false;
            if (!$idValidator->isOwner($this->streamId)) return false;
        } else {
            return false;
        }
        return true;
    }

    function doAction() {

        if ($this->brandSocialAccountId) {
            /** @var BrandSocialAccountService $brandService */
            $brandService = $this->createService('BrandSocialAccountService');
            $brandService->updateDisplayPanelLimit($this->brandSocialAccountId, $this->value);

        } elseif ($this->stream_prefix) {
            /** @var RssStreamService $service */
            $service = $this->createService($this->stream_prefix . 'Service');
            $service->updateDisplayPanelLimit($this->streamId, $this->value);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
