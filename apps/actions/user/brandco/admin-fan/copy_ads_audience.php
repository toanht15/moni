<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class copy_ads_audience extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data["audience_id"] = $this->GET['exts'][0];
    }

    public function validate() {
        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        if (!$validator->isValidAdsAudienceId($this->Data["audience_id"])) {
            return '404';
        }

        return true;
    }

    public function doAction() {

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        $audience = $ads_service->findAdsAudiencesById($this->Data["audience_id"]);

        $data["brand_user_relation_id"] = $audience->brand_user_relation_id;
        $data["name"] = $audience->name.' (copy)';
        $data["description"] = $audience->description;
        $data["search_condition"] = $audience->search_condition;
        $data["search_type"] = $audience->search_type;
        $data["status"] = AdsAudience::STATUS_DRAFT;

        $ads_service->createOrUpdateAdsAudience($data);

        return 'redirect: ' . Util::rewriteUrl('admin-fan', 'ads_list', array(), array("mid"=>"updated"));
    }
}
