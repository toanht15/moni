<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');
AAFW::import('jp.aainc.classes.services.BrandService');

class get_brand extends aafwGETActionBase {

    public $Secure = false;

    protected $brand_service;
    protected $AllowContent = array('JSON');

    public function validate () {
		return true;
	}

	function doAction() {
        if (!$this->enterprise_id || !$this->token) {
            $json_data = $this->createAjaxResponse('ng', array(), array('Invalid params'));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $this->brand_service = $this->createService('BrandService');
        $brand = $this->brand_service->getBrandByEnterpriseIdAndToken($this->enterprise_id, $this->token);

        $json_data = $this->createAjaxResponse('ok', $brand);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
