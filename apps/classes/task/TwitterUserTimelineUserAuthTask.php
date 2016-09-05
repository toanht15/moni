<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/01/29
 * Time: 9:50
 * To change this template use File | Settings | File Templates.
 */
AAFW::import('jp.aainc.classes.task.CrawlerTask');

require_once('vendor/codebird-php/src/codebird.php');

define('CODEBIRD_RETURNFORMAT_OBJECT', 0);
define('CODEBIRD_RETURNFORMAT_ARRAY', 1);

class TwitterUserTimelineUserAuthTask extends CrawlerTask {

	const GET_COUNT = 200;

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->crawler_type = $crawler_type;
		$this->crawler_service = $this->service_factory->create("CrawlerService");
		$this->stream_service = $this->service_factory->create("TwitterStreamService");
		$this->brand_social_account_service = $this->service_factory->create("BrandSocialAccountService");
		$this->crawler_urls = $this->crawler_service->getAvailableCrawlerUrlsByCrawlerTypeId($this->crawler_type->id);
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->config = aafwApplicationConfig::getInstance();
	}

	private function initClient() {
		\Codebird\Codebird::setConsumerKey(
			$this->config->query('@twitter.Admin.ConsumerKey'),
			$this->config->query('@twitter.Admin.ConsumerSecret')
		);

		$client = \Codebird\Codebird::getInstance();
		$client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
		return $client;
	}

	private function createParams($brand_social_account, $crawler_url) {
		if (!$crawler_url->url) {
			$params = array(
				'user_id' => $brand_social_account->social_media_account_id,
				'count' => self::GET_COUNT
			);
		} else {
			$query = explode("?", $crawler_url->url);
			$tmp_params = array();
			parse_str($query[1], $tmp_params);

			$params = array(
				'user_id' => $brand_social_account->social_media_account_id,
				'count' => self::GET_COUNT
			);

			if (!Util::isNullOrEmpty($tmp_params["since_id"])) {
				$params["since_id"] = $tmp_params["since_id"];
			}
		}
		return $params;
	}


	public function prepare() {
		$this->crawler_type->process_urls = count($this->crawler_urls);
		$this->crawler_service->updateCrawlerType($this->crawler_type);
	}

	public function crawl() {

		foreach ($this->crawler_urls as $crawler_url) {

			try {

				$target_object = explode("twitter_stream_", $crawler_url->target_id);
				$stream = $this->stream_service->getStreamById($target_object[1]);
				$brand_social_account = $stream->getBrandSocialAccount();

                if (Util::isNullOrEmpty($brand_social_account) || $brand_social_account->token_expired_flg) {
                    continue;
                }

				$client = $this->initClient();

				$client->setToken($brand_social_account->token, $brand_social_account->token_secret);

				$params = $this->createParams($brand_social_account, $crawler_url);
				$response = $client->statuses_userTimeline($params);

                //check errors
                $err_mess = $this->brand_social_account_service->getErrorMessage($brand_social_account, $response);
                if ($err_mess) {
                    $this->logger->error("TwitterUserTimelineUserAuthTask#crawl() Exception brand_social_account_id = " . $brand_social_account->id);
                    $this->logger->error($response);
                    continue;
                }

				$this->stream_service->doStore($stream, $crawler_url, $response);

			} catch (Exception $e) {
                $msg = $this->brand_social_account_service->getErrorMessage($brand_social_account, $e);
				$this->logError("TwitterUserTimelineUserAuthTask#crawl() Exception crawler_url_id = " . $crawler_url->id . "msg=" . $msg, $e);
			}
		}
	}

	public function finish() {
		$this->crawler_type->process_urls = 0;
		$this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
		$this->crawler_service->updateCrawlerType($this->crawler_type);
	}
}