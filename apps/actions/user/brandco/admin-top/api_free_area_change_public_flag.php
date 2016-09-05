<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class api_free_area_change_public_flag extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_free_area_change_public_flag';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	public function validate () {
        $this->Data['brand'] = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_FREE_AREA, $this->Data['brand']->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) return false;
		return true;
	}

	function doAction() {

		$service = $this->createService(BrandcoValidatorBase::SERVICE_NAME_FREE_AREA);

		$service->changePublicFlag($this->Data['brand']->id,$this->entryId);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
}