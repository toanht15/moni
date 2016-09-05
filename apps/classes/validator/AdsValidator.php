<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class AdsValidator extends aafwObject {

    private $brand_user_relation_id;

    /** @var AdsService $ads_service */
    private $ads_service;

    public function __construct($brand_user_relation_id) {
        $this->brand_user_relation_id = $brand_user_relation_id;
        $service_factory = new aafwServiceFactory();
        $this->ads_service = $service_factory->create("AdsService");
        parent::__construct();
    }

    public function validate() {
    }

    public function isValidAdsUserId($id) {

        if (!$id || !is_numeric($id)) {
            return false;
        }

        $ads_user = $this->ads_service->findAdsUserById($id);

        if (!$ads_user || $ads_user->brand_user_relation_id != $this->brand_user_relation_id) {
            return false;
        }

        return true;
    }

    public function isValidAdsAccountId($id) {

        if (!is_numeric($id) || !$id) {
            return false;
        }

        $account = $this->ads_service->findAdsAccountById($id);

        if (!$account) {
            return false;
        }

        return $this->isValidAdsUserId($account->ads_user_id);
    }

    public function isValidAdsAudienceId($id) {

        if (!is_numeric($id) || !$id) {
            return false;
        }

        $audience = $this->ads_service->findAdsAudiencesById($id);

        if (!$audience || $audience->brand_user_relation_id != $this->brand_user_relation_id) {
            return false;
        }

        return true;
    }
}