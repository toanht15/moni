<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_delete_free_area_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_delete_free_area_entry';
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

		$service->deleteEntry($this->Data['brand']->id,$this->entryId);

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
}