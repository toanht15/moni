<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class rss_entries extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	private $pageLimited = 20;

	public function validate () {
        $brand = $this->getBrand();
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_RSS,$brand->id);
		$this->Data['streamId'] = $this->GET['exts'][0];
		if(!$this->Data['streamId']) return false;
        if(!$idValidator->isOwner($this->Data['streamId'])) return false;

        $rss_entry_service = $this->createService('RssStreamService');
        $this->Data['stream'] = $rss_entry_service->getStreamById($this->Data['streamId']);
        $this->Data['pageLimited'] = $this->pageLimited;
        $this->Data['totalEntriesCount'] = $rss_entry_service->count($this->Data['streamId']);

        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $order = array(
            'name' => 'pub_date',
            'direction' => 'desc',
        );
        $this->Data['rssEntries'] = $rss_entry_service->getEntriesByStreamId($this->Data['streamId'], $this->p, $this->pageLimited, $order );

		return true;
	}

	function doAction() {

		return 'user/brandco/admin-top/rss_entries.php';
	}
}