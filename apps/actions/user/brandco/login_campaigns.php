<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.CampaignPageValidator');

class login_campaigns extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedUserLogin = true;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
    }

    public function validate() {

        $validator = new CampaignPageValidator($this->Data['cp_id']);
        $validator->validate();
        if (!$validator->isValid()) {
            return '404';
        }

        return true;
    }

    function doAction() {
        return 'redirect: ' . Util::rewriteUrl('', 'campaigns', array("cp_id" => $this->Data['cp_id']));
    }
}
