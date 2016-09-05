<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class edit_free_area_entry extends BrandcoPOSTActionBase {
	protected $ContainerName = 'edit_free_area_entry';
    protected $AllowContent = array('JSON', 'PHP');

	protected $Form = array (
		'package' => 'admin-top',
		'action' => 'edit_free_area_entry_form/{entryId}',
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	protected $ValidatorDefinition = array(
		'body' => array(
			'required' => true,
			'type' => 'str',
		),
	);

	public function validate () {

		return true;
	}

	function doAction() {
		$free_area_entry_service = $this->createService('FreeAreaEntryService');
		$brand = $this->getBrand();
		$freeAreaEntry = $free_area_entry_service->getEntryByBrandIdAndEntryId($brand->id, $this->entryId);
		if(!$freeAreaEntry) {
			$freeAreaEntry = $free_area_entry_service->createEmptyEntry();
			$freeAreaEntry->brand_id = $brand->id;
		}
		$freeAreaEntry->body = $this->body;

		$free_area_entry_service->createEntry($freeAreaEntry);

		$this->entryId = $freeAreaEntry->id;

		if($this->Validator->getErrorCount()) {
			$return = $this->getFormURL();
		} else{
			$this->resetActionContainerByName();
			$return = 'redirect: '.Util::rewriteUrl('admin-top', 'free_area_entries',null,array('p'=>$this->p));
		}
        if($this->preview) {
            $json_data = $this->createAjaxResponse("ok", array($freeAreaEntry->id));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }else {
            return $return;
        }
	}
}