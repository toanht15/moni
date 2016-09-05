<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class DeleteDisconnectedSNS {

    private $logger;
    private $service_factory;
    /** @var BrandSocialAccountService $brand_social_account_service */
    private $brand_social_account_service;
    /** @var  CrawlerService $crawler_service */
    private $crawler_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
        $this->crawler_service = $this->service_factory->create('CrawlerService');
    }

    public function doProcess() {
        //BrandSocialAccount削除
        $need_delete_social_accounts = $this->brand_social_account_service->getDisconnectedSNSOverMonth();
        /** @var EngagementSocialAccountService $engagement_social_account_service */
        $engagement_social_account_service = $this->service_factory->create('EngagementSocialAccountService');

        foreach ($need_delete_social_accounts as $brand_social_account) {
            if ($engagement_social_account_service->isUsedBrandSocialAccount($brand_social_account->id)) {
                continue;
            }
            if ($this->deleteSocialAccount($brand_social_account)) {
                $this->logger->info('DeleteDisconnectedSNS deleted brand_social_account_id = '.$brand_social_account->id.'social_app_id = '.$brand_social_account->social_app_id.' last_updated = '.$brand_social_account->updated_at);
            }
        }

        //RSS削除
        /** @var RssStreamService $rss_stream_service */
        $rss_stream_service = $this->service_factory->create('RssStreamService');
        $rss_stream_service->deleteDisconnectedRssOverMonth();

    }

    /**
     * @param BrandSocialAccount $brand_social_account
     * @return bool
     */
    public function deleteSocialAccount(BrandSocialAccount $brand_social_account) {
        $brand_social_account_store = $this->brand_social_account_service->getBrandSocialAccountStore();
        try {
            list($stream_service, $stream, $stream_type) = $this->getStreamServiceAndStream($brand_social_account);
            if (!$stream_service || !$stream || !$stream_type) {
                $this->logger->error('DeleteDisconnectedSNS cant get Stream or StreamService from $brand_social_account_id = '.$brand_social_account->id);
                return false;
            }

            $brand_social_account_store->begin();
            $this->crawler_service->deleteCrawlerUrlByTargetId($stream_type.'_stream_'.$stream->id);
            $stream_service->deletePhysicalStreamAndEntries($stream);
            $brand_social_account_store->deletePhysical($brand_social_account);
            $brand_social_account_store->commit();
        }catch (Exception $e) {
            $this->logger->error('DeleteDisconnectedSNS batch brand_social_account_id='.$brand_social_account->id);
            $this->logger->error($e);
            $brand_social_account_store->rollback();
            return false;
        }
        return true;
    }

    /**
     * @param $brand_social_account
     * @return array
     */
    public function getStreamServiceAndStream($brand_social_account) {
        if($brand_social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
            $facebook_stream_service = $this->service_factory->create('FacebookStreamService');
            $facebook_stream = $brand_social_account->getFacebookStream();
            return array($facebook_stream_service, $facebook_stream, $facebook_stream_service->getStreamType());

        } elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_TWITTER){
            $twitter_stream_service = $this->service_factory->create('TwitterStreamService');
            $twitter_stream = $brand_social_account->getTwitterStream();
            return array($twitter_stream_service, $twitter_stream, $twitter_stream_service->getStreamType());

        }elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_GOOGLE){
            $youtube_stream_service = $this->service_factory->create('YoutubeStreamService');
            $youtube_stream = $brand_social_account->getYoutubeStream();
            return array($youtube_stream_service, $youtube_stream, $youtube_stream_service->getStreamType());
        } elseif($brand_social_account->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
            $instagram_stream_service = $this->service_factory->create('InstagramStreamService');
            $instagram_stream = $brand_social_account->getInstagramStream();

            return array($instagram_stream_service, $instagram_stream, $instagram_stream_service->getStreamType());
        }
        return array();
    }
}
