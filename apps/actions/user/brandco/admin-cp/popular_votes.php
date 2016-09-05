<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class popular_votes extends BrandcoGETActionBase {
    protected $ContainerName = 'popular_votes';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        if (!$this->isLoginManager()) {
            return '403';
        }

        $popular_vote_validator = new CpDataManagerValidator($this->getBrand()->id, $this->Data['action_id'], CpAction::TYPE_POPULAR_VOTE);

        if (!$popular_vote_validator->validate()) {
            return '404';
        } else {
            $this->Data['pageData'] = $popular_vote_validator->getCpActionInfo();
            $this->Data['pageData']['brand_id'] = $this->brand->id;
        }
        return true;
    }

    public function doAction() {
        return 'user/brandco/admin-cp/popular_votes.php';
    }
}