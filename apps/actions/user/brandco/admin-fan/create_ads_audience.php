<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class create_ads_audience extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;

    protected $ContainerName = 'create_ads_audience';

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate () {
        return true;
    }

    function doAction() {

        $this->Data['brand_user_relation_id'] = $this->getBrandsUsersRelation()->id;
        $this->Data['brand_id'] = $this->getBrand()->id;

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');

        $ads_users = $ads_service->findAdsUsersByBrandUserRelationId($this->getBrandsUsersRelation()->id);
        $ads_service->updateAdsAccountInfo($ads_users);

        $this->Data['ads_accounts'] = $ads_service->findValidAdsAccountByBrandUserRelationId($this->getBrandsUsersRelation()->id);

        return 'user/brandco/admin-fan/create_ads_audience.php';
    }
}