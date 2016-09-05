<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class api_disconnect_social_app extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_disconnect_social_app';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	protected $stream;
	protected $service;

	public function beforeValidate () {
		$this->Data['brandSocialAccountId'] = $this->brandSocialAccountId;
        $this->Data['stream_prefix'] = $this->stream_prefix;
	}

	public function validate () {
        $this->brand = $this->getBrand();
        if($this->Data['brandSocialAccountId']){
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
            if(!$idValidator->isCorrectEntryId($this->Data['brandSocialAccountId'])) return false;

            $brandService = $this->createService('BrandSocialAccountService');
            $this->stream = $brandService->getStreamByBrandSocialAccountId($this->Data['brandSocialAccountId']);
            $this->service = $this->createService(get_class($this->stream).'Service');

        }elseif($this->Data['stream_prefix']){
            $idValidator = new StreamValidator($this->Data['stream_prefix'].'Service', $this->brand->id);
            if(!$idValidator->isPanelServiceName($this->Data['stream_prefix'].'Service')) return false;
            if(!$idValidator->isOwner($this->streamId)) return false;

            $this->service = $this->createService($this->Data['stream_prefix'].'Service');
            $this->stream = $this->service->getStreamById($this->streamId);
        }else{
            return false;
        }

		return true;
	}

	function doAction() {
        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->brand->id);

        try {
            $this->service->hideStreamAndCrawlerUrl($this->stream->id);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->info("api_disconnect_social_app Error");
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
	}
}