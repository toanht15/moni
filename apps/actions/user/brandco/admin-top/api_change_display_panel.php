<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');

class api_change_display_panel extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_change_display_panel';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	public function beforeValidate () {
	}

	public function validate () {

        $this->brand = $this->getBrand();
        if($this->stream.'Service' == BrandcoValidatorBase::SERVICE_NAME_LINK){
            $idValidator = new StaticEntryValidator($this->stream.'Service',$this->brand->id);
        } else {
            $idValidator = new StreamValidator($this->stream . 'Service', $this->brand->id);
        }
        if(!$idValidator->isPanelServiceName($this->stream.'Service')) return false;
        if(!$idValidator->isCorrectEntryId($this->entry_id)) return false;

		return true;
	}

	function doAction() {

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->brand->id);

		$service = $this->createService($this->stream.'Service');
		
		$entry = $service->getEntryById($this->entry_id);
		$service->changeDisplayType($entry);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
}