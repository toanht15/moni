<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class ads_list extends BrandcoGETActionBase {

    protected $ContainerName = 'ads_list';

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;

    public function validate () {
        return true;
    }

    function doAction() {

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        $this->Data['ads_users'] = $ads_service->findAdsUsersByBrandUserRelationId($this->getBrandsUsersRelation()->id);
        $this->Data['ads_audiences'] = $ads_service->findAdsAudiencesByBrandUserRelationId($this->getBrandsUsersRelation()->id);

        return 'user/brandco/admin-fan/ads_list.php';
    }
}
