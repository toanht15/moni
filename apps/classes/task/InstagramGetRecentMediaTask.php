<?php
AAFW::import('jp.aainc.classes.task.CrawlerTask');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class InstagramGetRecentMediaTask extends CrawlerTask {

    /** @var BrandSocialAccountService $service $brand_social_account_service */
    protected  $brand_social_account_service ;
    protected $hipchat_logger;

    public function __construct($crawler_type) {
        $this->service_factory = new aafwServiceFactory ();
        $this->crawler_type = $crawler_type;
        $this->crawler_service = $this->service_factory->create("CrawlerService");
        $this->stream_service = $this->service_factory->create("InstagramStreamService");
        $this->crawler_urls = $this->crawler_service->getAvailableCrawlerUrlsByCrawlerTypeId($this->crawler_type->id);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->config = aafwApplicationConfig::getInstance();
        $this->brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');

    }

    function prepare() {
        $this->crawler_type->process_urls = count($this->crawler_urls);
        $this->crawler_service->updateCrawlerType($this->crawler_type);
    }

    public function crawl() {
        foreach ($this->crawler_urls as $crawler_url) {

            $target_object = explode("instagram_stream_", $crawler_url->target_id);
            $stream = $this->stream_service->getStreamById($target_object[1]);

            $brand_social_account = $stream->getBrandSocialAccount();

            if(Util::isNullOrEmpty($brand_social_account)) continue;
            
            // If the error count reaches the maximum, it outputs an error.
            if ($brand_social_account->token_expired_flg) {
                $brand = $brand_social_account->getBrand();
                $this->logger->error('トークンの更新が必要です。' . $brand->name . '様までInstagramアカウント（' . $brand_social_account->name . '）の再連携の依頼をお願いします');
                continue;
            }

            try {
                $instagram = new Instagram();

                // 初期化済みか
                $is_initialized = $this->stream_service->getEntriesCountByStreamIds($stream->id);

                if ($is_initialized) {
                    $response = $instagram->getRecentMedia($brand_social_account->social_media_account_id, $brand_social_account->token, $crawler_url->url);

                    if (!$response || $err_mess = $this->brand_social_account_service->getErrorMessage($brand_social_account, $response)) {
                        $this->logger->error($response);
                        throw new Exception('InstagramGetRecentMediaTask: Instagram access denied');
                    }

                    $this->stream_service->doStore($stream, $crawler_url, $response);

                    while ($response->pagination->next_url) {

                        $response = $instagram->executeGETRequest($response->pagination->next_url . '&' . $crawler_url->url);

                        if (!$response || $err_mess = $this->brand_social_account_service->getErrorMessage($brand_social_account, $response)) {
                            $this->logger->error($response);
                            throw new Exception('InstagramGetRecentMediaTask: Instagram access denied');
                        }

                        $this->stream_service->doStore($stream, $crawler_url, $response);
                    }

                } else {
                    $response = $instagram->getRecentMedia($brand_social_account->social_media_account_id, $brand_social_account->token);

                    if (!$response || $err_mess = $this->brand_social_account_service->getErrorMessage($brand_social_account, $response)) {
                        $this->logger->error($response);
                        throw new Exception('InstagramGetRecentMediaTask: Instagram access denied');
                    }

                    $this->stream_service->doStore($stream, $crawler_url, $response, 'updated_at', true);

                    $instagram_entry_count = $this->stream_service->getEntriesCountByStreamIds($stream->id);

                    if ($instagram_entry_count >= InstagramEntry::INIT_CRAWL_COUNT) {
                        continue;
                    }

                    while ($response->pagination->next_url) {

                        $response = $instagram->executeGETRequest($response->pagination->next_url);

                        if (!$response || $err_mess = $this->brand_social_account_service->getErrorMessage($brand_social_account, $response)) {
                            $this->logger->error($response);
                            throw new Exception('InstagramGetRecentMediaTask: Instagram access denied');
                        }

                        $this->stream_service->doStore($stream, $crawler_url, $response, 'updated_at', true);
                    }

                }

            } catch (Exception $e) {

                // If the error count reaches the maximum, it outputs an error.
                if ($brand_social_account->token_expired_flg) {
                    $brand = $brand_social_account->getBrand();
                    $this->hipchat_logger->error('トークンの更新が必要です。' . $brand->name . '様までInstagramアカウント（' . $brand_social_account->name . '）の再連携の依頼をお願いします');
                    $this->logError('トークンの更新が必要です。InstagramGetRecentMediaTask#crawl() brand_social_account_id = '.$brand_social_account->id, $e);
                }
            }
        }
    }

    public function finish() {
        $this->crawler_type->process_urls = 0;
        $this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
        $this->crawler_service->updateCrawlerType($this->crawler_type);
    }
}