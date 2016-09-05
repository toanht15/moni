<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class link_entries extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	private $pageLimited = 20;

	public function validate () {
        $brand = $this->getBrand();
        $link_entry_service = $this->createService('LinkEntryService');
        $this->Data['pageLimited'] = $this->pageLimited;
        $this->Data['totalEntriesCount'] = $link_entry_service->count($brand->id);

        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $order = array(
            'name' => 'updated_at',
            'direction' => 'desc'
        );

        $this->Data['linkEntries'] = $link_entry_service->getEntriesByBrandId($brand->id, $this->p, $this->pageLimited, array(), $order);

		return true;
	}

	function doAction() {

		return 'user/brandco/admin-top/link_entries.php';
	}
}