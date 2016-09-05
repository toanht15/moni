<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class SearchBlockFacebookLike extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        $params['facebook_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($params['search_conditions']['brand_id'],SocialApps::PROVIDER_FACEBOOK);
        return $params;
    }

}