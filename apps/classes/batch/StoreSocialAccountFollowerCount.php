<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class StoreSocialAccountFollowerCount {
    public $logger;
    public $sevice_factory;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {
        $instagram = new Instagram();
        /** @var SocialAccountFollowerService $social_account_follower_service */
        $social_account_follower_service = $this->service_factory->create('SocialAccountFollowerService');
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

        // Instagramアカウントのフォローワーしか取ってません
        $accounts = $brand_social_account_service->getSnsAccounts(SocialApps::PROVIDER_INSTAGRAM);
        $dateLogged = date( "Y-m-d", strtotime('-1 day') );

        foreach ($accounts as $account) {
            try {
                $ret = $instagram->getAccountInfo($account->social_media_account_id, $account->token);
                if (!$ret || $ret->meta->code != Instagram::LEGAL_ACCESS_CODE) {
                    throw new Exception('StoreScioalAccountFollowerCount: Instagram access denied');
                }
                $social_account_follower_count = $social_account_follower_service->createEmptyBrandSocialAccount();
                $social_account_follower_count->date_logged = $dateLogged;
                $social_account_follower_count->brand_social_account_id = $account->id;
                $social_account_follower_count->value = $ret->data->counts->followed_by;
                $social_account_follower_service->createSocialAccountFollowerCount($social_account_follower_count);
            } catch (Exception $e) {
                $this->logger->error("StoreScioalAccountFollowerCount#doProcess() Exception brand_social_account = " . $account->social_media_account_id);
                $this->logger->error($e);
                continue;
            }
        }
    }

}
