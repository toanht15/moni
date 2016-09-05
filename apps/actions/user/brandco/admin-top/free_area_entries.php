<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class free_area_entries extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	protected $pageLimited = 20;

	public function validate () {

        $this->Data['limit'] = $this->pageLimited;

        $free_area_entry_service = $this->createService('FreeAreaEntryService');
        $brand = $this->getBrand();
        $this->Data['freeAreaCount'] = $free_area_entry_service->countEntriesByBrandId($brand->id);

        $total_page = floor ( $this->Data['freeAreaCount'] / $this->pageLimited ) + ( $this->Data['freeAreaCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $this->Data['freeAreaEntries'] = $free_area_entry_service->getEntriesByBrandId($brand->id,$this->p,$this->Data['limit']);

		return true;
	}

	function doAction() {

		return 'user/brandco/admin-top/free_area_entries.php';
	}
}