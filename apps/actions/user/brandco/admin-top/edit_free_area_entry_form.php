<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');

class edit_free_area_entry_form extends BrandcoGETActionBase {
	protected $ContainerName = 'edit_free_area_entry';

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	public function beforeValidate () {
		$this->Data['entryId'] = $this->GET['exts'][0];
        $this->deleteErrorSession();
	}

	public function validate (){

        $this->Data['brand'] = $this->getBrand();
        if ($this->Data['entryId'] != 0) {
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_FREE_AREA, $this->Data['brand']->id);
            if (!$idValidator->isCorrectEntryId($this->Data['entryId'])) return false;
        }

		return true;
	}

	function doAction() {
		$free_area_entry_service = $this->createService('FreeAreaEntryService');
		$freeAreaEntry = $free_area_entry_service->getEntryByBrandIdAndEntryId($this->Data['brand']->id, $this->Data['entryId']);
		if(!$freeAreaEntry) {
			$freeAreaEntry = $free_area_entry_service->createEmptyEntry();
		}
		$this->assign('ActionForm', $freeAreaEntry->toArray());

		return 'user/brandco/admin-top/edit_free_area_entry_form.php';
	}
}