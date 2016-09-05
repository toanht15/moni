<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SearchBlockTwitterFollow extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        $params['twitter_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($params['search_conditions']['brand_id'],SocialApps::PROVIDER_TWITTER);
        return $params;
    }

}