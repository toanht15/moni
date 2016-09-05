<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.services.BrandNotificationService');
AAFW::import('jp.aainc.classes.clients.UtilityApiClient');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');

class BrandcoAdminHeader extends aafwWidgetBase {

    public function doService($params = array() ) {
        $config = aafwApplicationConfig::getInstance();

        $service_factory = new aafwServiceFactory();
        /** @var UserService $user_service */
        $user_service = $service_factory->create('UserService');
        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $brand_user_relation_service = $service_factory->create('BrandsUsersRelationService');
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');

        $manager_service = $service_factory->create('ManagerService');
        $user = $user_service->getUserByMoniplaUserId($params['login_info']['userInfo']->id);
        $brands_users_relation = $brand_user_relation_service->getBrandsUsersRelation($params['login_info']['brand']->id, $user->id);

        $params['can_download_brand_fan_list'] = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_FAN_LIST);

        if ($config->query('@facebook.Admin.TokenExpiredCheck')) {
            /** @var BrandSocialAccountService $account_service */
            $account_service = $service_factory->create('BrandSocialAccountService');

            $brand_social_accounts = $account_service->getBrandSocialAccountsByUserId($user->id, SocialApps::PROVIDER_FACEBOOK);
            // 有効期限切れFBページ取得
            if (count($brand_social_accounts)) {
                $params['token_expiry_error_pages'] = $account_service->getFbAccessTokenExpiryAccounts($brand_social_accounts, BrandSocialAccount::FB_EXPIRED_DATE);
                // 有効期限切れFBページがない場合は2週間前のページを取得
                if (!count($this->params['token_expiry_error_pages'])) {
                    $params['token_expiry_alert'] = $account_service->getFbAccessTokenExpiryAccounts($brand_social_accounts, BrandSocialAccount::FB_EXPIRED_DATE - 14);
                }
            }
        }

        $cache_manager = new CacheManager();
        $last_count = $cache_manager->getCache("fc", array($params['login_info']['brand']->id, $user->id));
        $brands_users_count = $cache_manager->getCache("fc", array($params['login_info']['brand']->id));

        if($brands_users_count) {
            $diff_user_count = $brands_users_count - ($last_count ? $last_count : 0);
            $params['increased_fans'] = $diff_user_count > 0 ? '+'.$diff_user_count : $diff_user_count;
        }

        $brandNotificationService = new BrandNotificationService();
        $all_brand_notification = $brandNotificationService->getAllBrandNotificationAfterRegisteredAt($brands_users_relation->created_at);
        if($all_brand_notification) {
            $total_brand_notification = $all_brand_notification->total();
            $all_brand_readmark = $brandNotificationService->getBrandReadMarkInfoByBrandIdAndUserId($params['login_info']['brand']->id,$user->id);
            if ($all_brand_readmark) {
                $total_brand_readmark = $all_brand_readmark->total();
            }
            $total_non_read = $total_brand_notification - $total_brand_readmark;
            if ($total_non_read > 0) {
                $params['notification_non_read'] = $total_non_read;
            }
        }

        if(config('UtilityAPI')) {

            //$params['live800'] = urlencode("userId=".UtilityApiClient::getInstance()->getUserToken(UtilityApiClient::LIVE800, $params['login_info']['userInfo']->id)."&name=".$params['login_info']['userInfo']->name);
        }
        $params['is_closed_brand'] = $params['login_info']['brand']->isClosedBrand();
        $params['isAgent'] = $manager_service->isAgentLogin();

        // 新規お問い合わせ数の取得
        /** @var InquiryService $inquiry_service */
        $inquiry_service = $this->getService('InquiryService');
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => $params['login_info']['brand']->id));
        $params['inquiry_non_read'] = $inquiry_service->countRecord(InquiryService::MODEL_TYPE_INQUIRY_ROOMS, array(
            'inquiry_brand_id' => $inquiry_brand->id,
            'operator_type' => InquiryRoom::TYPE_ADMIN,
            'status' => array(InquiryRoom::STATUS_OPEN),
        ));

        $params['has_ads_option'] = $this->hasAdsOption($params['login_info']['brand']);

        return $params;
    }

    public function hasAdsOption($brand) {
        $options = BrandInfoContainer::getInstance()->getBrandOptions();
        return $brand->hasOption(BrandOptions::OPTION_FACEBOOK_ADS, $options) || $brand->hasOption(BrandOptions::OPTION_TWITTER_ADS, $options);
    }
}
