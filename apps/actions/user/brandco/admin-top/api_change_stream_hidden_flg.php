<?php
AAFW::import ( 'jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase' );
AAFW::import('jp.aainc.classes.CacheManager');
class api_change_stream_hidden_flg extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_change_stream_hidden_flg';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;
	
	public function beforeValidate() {
	}
	public function validate() {
        $this->brand = $this->getBrand();
        if($this->brandSocialAccountId) {
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
            if (!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) return false;
        }elseif($this->stream_prefix){
            $idValidator = new StreamValidator($this->stream_prefix . 'Service', $this->brand->id);
            if(!$idValidator->isPanelServiceName($this->stream_prefix.'Service')) return false;
            if (!$idValidator->isOwner($this->streamId)) return false;
        }else{
            return false;
        }
		return true;
	}
	function doAction() {

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->brand->id);

        if($this->brandSocialAccountId){
            $brandService = $this->createService('BrandSocialAccountService');
            $stream = $brandService->getStreamByBrandSocialAccountId($this->brandSocialAccountId);
            $service = $this->createService(get_class($stream).'Service');
        }elseif($this->stream_prefix){
            $service = $this->createService($this->stream_prefix.'Service');
            $stream = $service->getStreamById($this->streamId);
        }

		$service->changeEntryHiddenFlgForStream($stream->id,$this->value);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
}
