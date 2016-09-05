<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/09/29
 * Time: 20:14
 */

require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class CountDailyYoutubeChannelSubscriber {

    private $logger;
    private $serviceFactory;
    private $brandSocialAccountService;
    private $cpYoutubeChannelUserLogService;
    private $socialAccountFollowerService;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->serviceFactory = new aafwServiceFactory();

        $this->brandSocialAccountService                  = $this->serviceFactory->create('BrandSocialAccountService');
        $this->socialAccountFollowerService               = $this->serviceFactory->create('SocialAccountFollowerService');
        $this->cpYoutubeChannelUserLogService             = $this->serviceFactory->create('CpYoutubeChannelUserLogService');
    }

    public function doProcess() {
        $googleAccounts = $this->brandSocialAccountService->getSnsAccounts(SocialApps::PROVIDER_GOOGLE);

        if (count($googleAccounts)) {
            $dateLogged = date( "Y-m-d", strtotime('-1 day') );

            foreach ($googleAccounts as $googleAccount) {
                try {
                    $channelId                  = $googleAccount->getYoutubeChannelId();
                    $accessToken                = $googleAccount->token;
                    $subscriberCount            = $this->cpYoutubeChannelUserLogService->getYoutubeChannelSubscriberCount($accessToken, $channelId);
                    $socialAccountFollowerCount = $this->socialAccountFollowerService->createEmptyBrandSocialAccount();

                    $socialAccountFollowerCount->date_logged             = $dateLogged;
                    $socialAccountFollowerCount->brand_social_account_id = $googleAccount->id;
                    $socialAccountFollowerCount->value                   = $subscriberCount;

                    $this->socialAccountFollowerService->createSocialAccountFollowerCount($socialAccountFollowerCount);
                } catch (Exception $e) {
                    $this->logger->error("CountDailyYoutubeChannelSubscriber#doProcess() Exception brand_social_account = " . $googleAccount->social_media_account_id);
                    $this->logger->error($e);
                    continue;
                }
            }
        }
    }
}