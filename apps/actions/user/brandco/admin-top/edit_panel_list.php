<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class edit_panel_list extends BrandcoGETActionBase {

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	protected $stream;
	protected $image_url;
	protected $service;
	protected $pageLimited = 20;

	public function beforeValidate () {
		if (!$this->isRealInteger($this->p) || $this->p < 1) $this->p = 1;
		$this->Data['brandSocialAccountId'] = $this->GET['exts'][0];
	}

	public function validate () {

        $brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $brand->id);
        if(!$idValidator->isCorrectEntryId($this->Data['brandSocialAccountId'])) return false;

		$brandService = $this->createService('BrandSocialAccountService');
		$socialAccount = $brandService->getBrandSocialAccountById($this->Data['brandSocialAccountId']);
        $this->Data['display_panel_limit'] = $socialAccount->display_panel_limit;
		$this->image_url = $socialAccount->picture_url;
		$this->stream = $brandService->getStreamByBrandSocialAccountId($this->Data['brandSocialAccountId']);
		$this->service = $this->createService(get_class($this->stream).'Service');

        $this->Data['totalEntriesCount'] = $this->service->getEntriesCountByStreamIds($this->stream->id);
        $totalPages = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $totalPages);

		if ($this->isEmpty($this->sort)) {
			$this->sort = "pub_date";
		} else {
			$sortFields = $this->service->getSortFields();
			if( !in_array($this->sort, $sortFields) ){
				return 404;
			}
		}

		// order
		if ($this->isEmpty($this->order)) {
			$this->order = "desc";
		}

		$this->order = array(
			'name' => $this->sort,
			'direction' => $this->order,
		);

		return true;
	}

	function doAction() {

		$this->Data['entries'] = $this->service->getEntriesByStreamId($this->stream->id, $this->p, $this->pageLimited, $this->order);
		$this->Data['stream'] = $this->stream;
		$this->Data['image_url'] = $this->image_url;
		$this->Data['pageLimited'] = $this->pageLimited;	
		return 'user/brandco/admin-top/edit_panel_list.php';
	}
}