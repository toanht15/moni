<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class FreeAreaEntryService extends aafwServiceBase {

	private $entries;
	private $brands;

	public function __construct() {
		$this->entries = $this->getModel("FreeAreaEntries");
		$this->brands = $this->getModel("Brands");
	}

	public function getEntriesByBrandId($brandId, $page = 1, $limit = 20, $params = array(), $order = array('name' => 'public_flg DESC, updated_at', 'direction' => 'DESC')) {
		$filter = array(
			'conditions' => array(
				"brand_id" => $brandId,
			),
			'pager' => array(
				'page' => $page,
				'count' => $limit,
			),
			'order' => $order,
		);
        if( isset($params['free_area_entry_id']) ){
            $filter['conditions']['id'] = $params['free_area_entry_id'];
        }

		return $this->entries->find($filter);
	}
	
	public function countEntriesByBrandId($brandID){
		$filter = array(
			'conditions' => array(
				"brand_id" => $brandID,
			),
			'pager' => array(),
			'order' => array(),
		);
		return $this->entries->count($filter);
	}
	
	public function changePublicFlag($brand_id,$entry_id){
		$filter = array(
				'conditions' => array(
						"brand_id" => $brand_id,
						"public_flg" => '1',
				),
				'pager' => array(),
				'order' => array(),
		);
		$publicEntry = $this->entries->findOne($filter);
		if($publicEntry){
			$publicEntry->public_flg = '0';
			$this->entries->save($publicEntry);
		}
		$filter = array(
			'conditions' => array(
				"brand_id" => $brand_id,
				"id" => $entry_id,
			),
			'pager' => array(),
			'order' => array(),
		);
		
		$entry = $this->entries->findOne($filter);
		if($entry->id != $publicEntry->id){
			$entry->public_flg = '1';
		}
		$this->entries->save($entry);
	}
	
	public function deleteEntry($brandId, $entry_id){
		$filter = array(
			"conditions" => array(
			"brand_id" => $brandId,
					"id" => $entry_id,
		),
				"page" => array(),
				"order" => array(),
		);
		$entry = $this->entries->findOne($filter);
		$this->entries->deletePhysical($entry);
	}
	
	public function getSelectedEntryByBrandId($brandId) {
		$filter = array(
			"brand_id" => $brandId,
			"public_flg" => 1,
		);

		return $this->entries->findOne($filter);
	}

	public function getEntryByBrandIdAndEntryId($brandId, $entryId) {
		$filter = array(
			"id" => $entryId,
			"brand_id" => $brandId,
		);

		return $this->entries->findOne($filter);
	}

    public function getEntryById($id){
        $filter = array(
            "id" => $id,
        );
        return $this->entries->findOne($filter);
    }

	public function createEmptyEntry() {
		return $this->entries->createEmptyObject();
	}

	public function createEntry($entry) {

			$this->entries->save($entry);
	}
}