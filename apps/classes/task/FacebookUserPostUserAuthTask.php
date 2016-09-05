<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/01/29
 * Time: 9:50
 * To change this template use File | Settings | File Templates.
 */
AAFW::import('jp.aainc.classes.task.CrawlerTask');

class FacebookUserPostUserAuthTask extends CrawlerTask {

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->crawler_type = $crawler_type;
		$this->crawler_service = $this->service_factory->create("CrawlerService");
		$this->stream_service = $this->service_factory->create("FacebookStreamService");
		$this->crawler_urls = $this->crawler_service->getAvailableCrawlerUrlsByCrawlerTypeId($this->crawler_type->id);
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->config = aafwApplicationConfig::getInstance();
	}

	private function createParams($brand_social_account, $crawler_url) {
		if (!$crawler_url->url) {
			$params = "/" . $brand_social_account->social_media_account_id . "/feed?fields=id,from,link,message,type,picture,status_type,object_id,updated_time,created_time,description,story,name,full_picture,actions";
		} else {
			$params = "/" . $brand_social_account->social_media_account_id . "/feed";
            $params = $params . "?" . $crawler_url->url."&fields=id,from,link,message,type,picture,status_type,object_id,updated_time,created_time,description,story,name,full_picture,actions";
		}
		return $params;
	}

	public function prepare() {
		$this->crawler_type->process_urls = count($this->crawler_urls);
		$this->crawler_service->updateCrawlerType($this->crawler_type);
	}


	public function crawl() {

        $facebook_client = new FacebookApiClient();

		foreach ($this->crawler_urls as $crawler_url) {

			$target_object = explode("facebook_stream_", $crawler_url->target_id);
			$stream = $this->stream_service->getStreamById($target_object[1]);

            if (!$stream) {
                continue;
            }

			$brand_social_account = $stream->getBrandSocialAccount();

            if (Util::isNullOrEmpty($brand_social_account) || $brand_social_account->token_expired_flg) {
                continue;
            }

			try {

                $facebook_client->setToken($brand_social_account->token);

                $params = $this->createParams($brand_social_account, $crawler_url);

                $response = $facebook_client->getResponse('GET', $params, array());

                $this->stream_service->doStore($stream, $crawler_url, $response);

			} catch (Exception $e) {
                $service_factory = new aafwServiceFactory();
                /** @var BrandSocialAccountService $brand_social_account_service */
                $brand_social_account_service = $service_factory->create('BrandSocialAccountService');
                $msg = $brand_social_account_service->getErrorMessage($brand_social_account, $e);
                $this->logError("FacebookUserPostUserAuthTask#crawl() Exception crawler_url_id = " . $crawler_url->id . " msg=" . $msg , $e);
			}
		}
	}

	public function finish() {
		$this->crawler_type->process_urls = 0;
		$this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
		$this->crawler_service->updateCrawlerType($this->crawler_type);
	}
}