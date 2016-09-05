<?php

AAFW::import('jp.aainc.classes.task.CrawlerTask');

require_once('vendor/google/Google_Client.php');
require_once('vendor/google/contrib/Google_YouTubeService.php');
require_once('vendor/google/contrib/Google_Oauth2Service.php');

class YoutubeUserPostUserAuthTask extends CrawlerTask {

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->crawler_type = $crawler_type;
		$this->crawler_service = $this->service_factory->create("CrawlerService");
		$this->brand_social_account_service = $this->service_factory->create("BrandSocialAccountService");
		$this->stream_service = $this->service_factory->create("YoutubeStreamService");
		$this->crawler_urls = $this->crawler_service->getAvailableCrawlerUrlsByCrawlerTypeId($this->crawler_type->id);
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->config = aafwApplicationConfig::getInstance();
	}

	private function initClient($brand_id) {
		$client = new Google_Client();
    	$client->setClientId($this->config->query('@google.Google.ClientID'));
    	$client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
    	$client->setRedirectUri(Util::getHttpProtocol().'://'. Util::getMappedServerName($brand_id) . '/'.$this->config->query('@google.Google.RedirectUri'));
    	$scope = array();
    	$apiBase = $this->config->query('@google.Google.ApiBaseUrl');
    	foreach ($this->config->query('@google.Google.Scope') as $url) {
    		array_push($scope, $apiBase.'/'.$url);
    	}
    	$client->setScopes($scope);
		return $client;
	}

	public function prepare() {
        $this->crawler_type->process_urls = count($this->crawler_urls);
        $this->crawler_service->updateCrawlerType($this->crawler_type);
	}

	public function crawl() {

		foreach ($this->crawler_urls as $crawler_url) {

			try {

				$target_object = explode("youtube_stream_", $crawler_url->target_id);
				$stream = $this->stream_service->getStreamById($target_object[1]);
				$brand_social_account = $stream->getBrandSocialAccount();

                if (Util::isNullOrEmpty($brand_social_account) || $brand_social_account->token_expired_flg) {
                    continue;
                }

				$client = $this->initClient($stream->brand_id);
				$client->setAccessToken($brand_social_account->token);

				$youtube = new Google_YouTubeService($client);

                $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
                    'mine' => 'true',
                ));
                foreach ($channelsResponse['items'] as $channel) {
                    $playlistItems = $this->stream_service->getYoutubeVideoInfo($channel, $youtube);
				    $this->stream_service->doStore($stream, $crawler_url, $playlistItems);
				}

			} catch (Exception $e) {
                $service_factory = new aafwServiceFactory();
                /** @var BrandSocialAccountService $brand_social_account_service */
                $brand_social_account_service = $service_factory->create('BrandSocialAccountService');
                $msg = $brand_social_account_service->getErrorMessage($brand_social_account, $e);
				$this->logError("YoutubeUserPostUserAuthTask#crawl() Exception crawler_url_id = " . $crawler_url->id . " msg=" . $msg, $e);
			}
		}
	}

	public function finish() {
        $this->crawler_type->process_urls = 0;
        $this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
        $this->crawler_service->updateCrawlerType($this->crawler_type);
	}

}