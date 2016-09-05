<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class FacebookMarketingValidator extends aafwObject {

    private $brand_user_relation_id;
    /** @var  FacebookMarketingService $facebook_marketing_service */
    private $facebook_marketing_service;

    public function __construct($brand_user_relation_id) {
        $this->brand_user_relation_id = $brand_user_relation_id;
        $service_factory = new aafwServiceFactory();
        $this->facebook_marketing_service = $service_factory->create("FacebookMarketingService");
        parent::__construct();
    }

    public function validate() {

    }

    public function isValidMarketingUserId($id) {
        if (!$id || !is_numeric($id)) {
            return false;
        }
        $marketing_user = $this->facebook_marketing_service->getMarketingUserById($id);
        if (!$marketing_user || $marketing_user->brand_user_relation_id != $this->brand_user_relation_id) {
            return false;
        }
        return true;
    }

    public function isValidMarketingAccountId($id) {
        if (!is_numeric($id) || !$id) {
            return false;
        }
        $account = $this->facebook_marketing_service->getAccountById($id);
        if (!$account) {
            return false;
        }
        return $this->isValidMarketingUserId($account->marketing_user_id);
    }

    public function isValidMarketingAudienceId($id) {
        if (!is_numeric($id) || !$id) {
            return false;
        }
        $audience = $this->facebook_marketing_service->getAudienceById($id);
        if (!$audience) {
            return false;
        }
        return $this->isValidMarketingAccountId($audience->account_id);
    }

}