<?php

AAFW::import('jp.aainc.classes.task.CrawlerTask');

class FacebookGetPostsDetailUserAuthTask extends CrawlerTask {
    protected $streams;

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->crawler_type = $crawler_type;
		$this->crawler_service = $this->service_factory->create("CrawlerService");
		$this->brand_social_account_service = $this->service_factory->create("BrandSocialAccountService");
		$this->stream_service = $this->service_factory->create("FacebookStreamService");
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->config = aafwApplicationConfig::getInstance();
	}

	public function prepare() {
        $this->streams = $this->stream_service->getStreamsForUpdateDetail();
        if ($this->streams) {
            $this->crawler_type->process_urls = $this->streams->total();
            $this->crawler_service->updateCrawlerType($this->crawler_type);
        }
	}

	public function crawl() {
        $facebook_client = new FacebookApiClient();

		foreach ($this->streams as $stream) {
            if (!$stream) {
                continue;
            }
			$brand_social_account = $stream->getBrandSocialAccount();

            if (Util::isNullOrEmpty($brand_social_account) || $brand_social_account->token_expired_flg) {
                continue;
            }

            try {
                $facebook_client->setToken($brand_social_account->token);

                $batch_request_array = $facebook_client->createParamForUpdateEntry($stream);

                for ($i = 0; $i < count($batch_request_array); $i++) {
                    $requestParams = array();
                    $requestParams ['batch'] = $batch_request_array [$i];
                    $responses = $facebook_client->getPostsDetail($requestParams);

                    if ($responses[0]->code != 200) {
                        $error_entry = $this->stream_service->getEntriesForUpdateDetail($stream->id)->current();
                        $error_entry->detail_data_update_error_count += 1;
                        $this->stream_service->updateEntry($error_entry);
                        continue;
                    }

                    $facebook_client->updateFacebookEntries($responses, $stream);
                }

            } catch (Exception $e) {
                $msg = $this->brand_social_account_service->getErrorMessage($brand_social_account, $e);
                $this->logError("FacebookUserPostUserAuthTask#crawl() Exception stream_id = " . $stream->id . " msg=" . $msg, $e);
            }
		}
	}

	public function finish() {
        $this->crawler_type->process_urls = 0;
        $this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
        $this->crawler_service->updateCrawlerType($this->crawler_type);
	}
}
