<?php
AAFW::import('jp.aainc.classes.services.StreamService');

class LinkEntryService extends aafwServiceBase {

	private $entries;

	public function __construct() {
		$this->entries = $this->getModel("LinkEntries");
	}

	public function getEntriesByBrandId($brandId, $page = 1, $limit = 20, $params = array(), $order = null) {
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
		if( isset($params['keyword']) ){
			$filter['conditions']['body:like'] = "%" . $params['keyword'] . "%";
		}
		if( isset($params['hidden_flg']) ){
			$filter['conditions']['hidden_flg'] = $params['hidden_flg'];
		}

		return $this->entries->find($filter);
	}

	public function getEntryByBrandIdAndPageUrl($brandId, $pageUrl) {
		$filter = array(
				"link" => $pageUrl,
				"brand_id" => $brandId,
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

	public function count($brandId){
		$filter = array(
				"brand_id" => $brandId
		);
		return $this->entries->count($filter);
	}

	public function createEmptyEntry() {
		return $this->entries->createEmptyObject();
	}

	public function createEntry($entry) {

		return $this->entries->save($entry);
	}

	public function deleteEntryByBrandAndEntryId ($brandId, $entryId){
		$filter = array(
			"brand_id" => $brandId,
			"id" => $entryId,
		);
		$entry = $this->entries->findOne($filter);
		$this->entries->deletePhysical($entry);
	}

	public function getEntryById($entryId) {
		$conditions = array(
			"id" => $entryId,
		);
		return $this->entries->findOne($conditions);
	}

	public function changeDisplayType($entry){
        switch ($entry->display_type) {
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_SMALL:
                $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_MIDDLE;
                break;
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_MIDDLE:
                $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_LARGE;
                break;
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_LARGE:
                $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_SMALL;
                break;
            default:
                break;
        }
		$this->entries->save($entry);
	}

	public function updateEntryPriority ( $entry, $value ){
		$entry->priority_flg = $value;
		$this->entries->save($entry);
	}

	public function getAllHiddenEntries($brand_id, $page, $count, $order) {

		$filter = array(
			"conditions"=>array(
				"brand_id" => $brand_id,
				"hidden_flg" => 1
				),
		);
		return $this->entries->find($filter);
	}

	public function getAvailableEntriesByBrandId($brand_id) {
		$filter = array(
			"conditions" => array(
				'brand_id' => $brand_id,
				'hidden_flg' => 0
			),
		);

		return $this->entries->find($filter);
	}

	/**
	 * @param $link
	 * @return mixed
	 */
	public function getEntryByLink($link) {
		$filter = array(
			"link" => $link
		);
		return $this->entries->findOne($filter);
	}

    public function getEntryByCpLink($cp_id, $brand_id, $directory_name) {

        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'link:regexp' => '^https?://'. Util::getMappedServerName($brand_id) . '/' . Util::resolveDirectoryPath($brand_id, $directory_name) . 'campaigns/'.$cp_id.'($|\?)'
            ),
        );

        return $this->entries->find($filter);
    }
}