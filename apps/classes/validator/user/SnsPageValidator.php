<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class SnsPageValidator extends BaseValidator {

    private $brand_id;
    private $brand_social_account_id;

    private $service_factory;

    public function __construct($brand_social_account_id, $brand_id) {
        parent::__construct();

        $this->brand_id = $brand_id;
        $this->brand_social_account_id = $brand_social_account_id;

        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {
        if (trim($this->brand_social_account_id) == '') {
            $this->errors[$this->brand_social_account_id][] = 'ブランドソーシャルアカウントが存在しません';
            return;
        }

        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

        $brand_social_account = $brand_social_account_service->getBrandSocialAccountById($this->brand_social_account_id);
        if ($this->brand_id != $brand_social_account->brand_id) {
            $this->errors[$this->brand_social_account_id][] = 'ブランドソーシャルアカウントが存在しません';
            return;
        }

        if ($brand_social_account->hidden_flg == 1) {
            $this->errors[$this->brand_social_account_id][] = 'このブランドソーシャルアカウントは使えません';
            return;
        }

        if (!in_array($brand_social_account->social_app_id, SocialApps::$social_pages)) {
            $this->errors[$this->brand_social_account_id][] = 'このブランドソーシャルアカウントは使えません';
            return;
        }

        $stream = $brand_social_account_service->getStreamByBrandSocialAccountId($this->brand_social_account_id);
        if (!$stream) {
            $this->errors[$this->brand_social_account_id][] = 'このブランドソーシャルアカウントは使えません';
            return;
        }

        if ($stream->hidden_flg == 1) {
            $this->errors[$this->brand_social_account_id][] = 'このブランドソーシャルアカウントは使えません';
            return;
        }
    }
}