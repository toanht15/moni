<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class select_ads_sns_type extends BrandcoGETActionBase {

    protected $ContainerName = 'select_ads_sns_type';

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS,BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        if($this->redirect) {
            return 'redirect: ' . Util::rewriteUrl('admin-fan', 'ads_list', array(), array('showModal'=> 'select_sns'));
        }

        if($this->callback_url) {
            $this->Data['callback_url'] = $this->callback_url;
        }

        return 'user/brandco/admin-fan/select_ads_sns_type.php';
    }
}
