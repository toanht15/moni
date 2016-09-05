<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class select_instagram_entry extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    protected $stream;
    protected $image_url;
    protected $service;
    protected $pageLimited = 20;
    private $errorFlg;

    public function beforeValidate () {
        if (!$this->isRealInteger($this->p) || $this->p < 1) $this->p = 1;
        $this->Data['brandSocialAccountId'] = $this->tgt_act_id;
        $this->Data['cp_action_id'] = $this->action_id;
    }

    public function validate () {
        if ($this->Data['brandSocialAccountId'] == CpInstagramFollowAction::NO_TARGET_ACCOUNT) {
            $this->errorFlg = true;
            return true;
        }

        $this->Data['brand'] = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->Data['brand']->id);
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
        if($this->errorFlg){
            return $this->getErrorViewUrl();
        }

        $this->Data['entries'] = $this->service->getEntriesByStreamId($this->stream->id, $this->p, $this->pageLimited, $this->order);
        $this->Data['stream'] = $this->stream;
        $this->Data['image_url'] = $this->image_url;
        $this->Data['pageLimited'] = $this->pageLimited;
        return $this->getReturnUrl();
    }

    private function getReturnUrl(){
        return 'user/brandco/admin-cp/select_instagram_entry.php';
    }

    private function getErrorViewUrl(){
        return 'user/brandco/admin-cp/select_error_entry.php';
    }
}