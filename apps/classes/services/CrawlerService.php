<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CrawlerService extends aafwServiceBase {

	public function __construct() {
		$this->crawler_hosts = $this->getModel("CrawlerHosts");
		$this->crawler_types = $this->getModel("CrawlerTypes");
		$this->crawler_urls = $this->getModel("CrawlerUrls");
	}

	public function doCrawl(CrawlerType $crawler_type) {
		$crawler_task = $this->createCrawlerTask($crawler_type);

		if (extension_loaded('newrelic')) {
            $config = aafwApplicationConfig::getInstance();
            if($config->NewRelic['use']) {
                newrelic_name_transaction(get_class($crawler_task));
            }
		}

		$crawler_task->doExecute();
	}

	private function createCrawlerTask(CrawlerType $crawler_type) {
		$class = $crawler_type->task_name;
		AAFW::import('jp.aainc.classes.task.' . $class);
		return new $class($crawler_type);
	}

	#-----------------------------------------------------------
	# CrawlerHosts
	#-----------------------------------------------------------

	public function getHostByUrl($url) {
		$url_parts = parse_url($url);
		$host = $url_parts["host"];
		return $host;
	}

	public function getCrawlerHostByHost($host) {

		$filter = array(
			"name" => $host,
		);

		return $this->crawler_hosts->findOne($filter);
	}

	private function createEmptyCrawlerHost() {
		return $this->crawler_hosts->createEmptyObject();
	}

	private function createCrawlerHost($crawler_host) {
		$this->crawler_hosts->save($crawler_host);
	}

	public function createCrawlerHostByHost($host) {
		$crawler_host = $this->createEmptyCrawlerHost();
		$crawler_host->name = $host;
		$crawler_host->robots_file_last_modified = '';
		$crawler_host->robots_file_file_size = '';
		$crawler_host->robots_file_body = '';
		$crawler_host->robots_file_result = '0';
		$crawler_host->ignore_robots_file = '0';
		$this->createCrawlerHost($crawler_host);
	}

	#-----------------------------------------------------------
	# CrawlerTypes
	#-----------------------------------------------------------
	public function getAvailableCrawlerTypes() {

		$filter = array(
			'conditions' => array(
				'del_flg' => 0,
				'stop_flg' => 0,
			),
			'order' => array(
				'name' => 'name',
				'direction' => 'asc',
			),
		);

		return $this->crawler_types->find($filter);
	}

	public function updateCrawlerType(CrawlerType $crawler_type) {
		$this->crawler_types->save($crawler_type);
	}

	public function getCrawlerTypeByName($name) {
		$filter = array(
			"name" => $name,
		);
		return $this->crawler_types->findOne($filter);
	}

	#-----------------------------------------------------------
	# CrawlerUrls
	#-----------------------------------------------------------

	public function getCrawlerUrlsByCrawlerTypeId($crawler_type_id) {
		$conditions = array(
			"crawler_type_id" => $crawler_type_id,
			'del_flg' => 0,
		);
		return $this->crawler_urls->find($conditions);
	}

	public function getAvailableCrawlerUrlsByCrawlerTypeId($crawler_type_id) {
		$conditions = array(
			"crawler_type_id" => $crawler_type_id,
			"hidden_flg" => 0,
			"stop_flg" => 0,
		);
		return $this->crawler_urls->find($conditions);
	}

	private function createEmptyCrawlerUrl() {
		return $this->crawler_urls->createEmptyObject();
	}

	private function createCrawlerUrl($crawler_url) {
		$this->crawler_urls->save($crawler_url);
	}

	public function updateCrawlerUrl(CrawlerUrl $crawler_url) {
		$this->crawler_urls->save($crawler_url);
	}

	/**
	 * @param $stream
	 */
	public function createTwitterCrawlerUrl($stream) {

		// crawler_host
		$host = SocialApps::getSocialMediaProviderName(SocialApps::PROVIDER_TWITTER);
		$crawler_host = $this->getCrawlerHostByHost($host);

		// crawler_type
		$crawler_type_name = CrawlerTypes::getCrawlerTwitterTypeName();
		$crawler_type = $this->getCrawlerTypeByName($crawler_type_name);

		// crawler_url
		$crawler_url = $this->createEmptyCrawlerUrl();
		$crawler_url->crawler_type_id = $crawler_type->id;
		$crawler_url->crawler_host_id = $crawler_host->id;
		$crawler_url->target_id = StreamService::STREAM_TYPE_TWITTER . "_stream_" . $stream->id;
		$crawler_url->content_type = '';
		$crawler_url->url = '';
		$crawler_url->last_modified = '';
		$crawler_url->etag = '';
		$crawler_url->file_size = '';
		$crawler_url->title = '';
		$crawler_url->content = '';
		$crawler_url->last_crawled_date = date('Y-m-d H:i:s');

		// TODO
		$crawler_url->crawl_interval = 86400;
		$last_crawled_date = strtotime($crawler_url->last_crawled_date);
		$next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

		$crawler_url->next_crawled_date = $next_crawled_date;
		$crawler_url->result = 0;
		$crawler_url->time_out = 0;
		$crawler_url->status_code = 0;
		$this->createCrawlerUrl($crawler_url);
	}

	/**
	 * @param $stream
	 */
	public function createFacebookCrawlerUrl($stream) {

		// crawler_host
		$host = SocialApps::getSocialMediaProviderName(SocialApps::PROVIDER_FACEBOOK);
		$crawler_host = $this->getCrawlerHostByHost($host);

		// crawler_type
		$crawler_type_name = CrawlerTypes::getCrawlerFacebookTypeName();
		$crawler_type = $this->getCrawlerTypeByName($crawler_type_name);

		$crawler_url = $this->createEmptyCrawlerUrl();
		$crawler_url->crawler_type_id = $crawler_type->id;
		$crawler_url->crawler_host_id = $crawler_host->id;
		$crawler_url->target_id = StreamService::STREAM_TYPE_FACEBOOK . "_stream_" . $stream->id;
		$crawler_url->content_type = '';
		$crawler_url->url = '';
		$crawler_url->last_modified = '';
		$crawler_url->etag = '';
		$crawler_url->file_size = '';
		$crawler_url->title = '';
		$crawler_url->content = '';
		$crawler_url->last_crawled_date = date('Y-m-d H:i:s');

		// TODO
		$crawler_url->crawl_interval = 86400;
		$last_crawled_date = strtotime($crawler_url->last_crawled_date);
		$next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

		$crawler_url->next_crawled_date = $next_crawled_date;
		$crawler_url->result = 0;
		$crawler_url->time_out = 0;
		$crawler_url->status_code = 0;
		$this->createCrawlerUrl($crawler_url);
	}
	
	/**
	 * @param $stream
	 */
	public function createYoutubeCrawlerUrl($stream) {
	
		// crawler_host
		$host = SocialApps::getSocialMediaProviderName(SocialApps::PROVIDER_GOOGLE);
		$crawler_host = $this->getCrawlerHostByHost($host);
	
		// crawler_type
		$crawler_type_name = CrawlerTypes::getCrawlerYoutubeTypeName();
		$crawler_type = $this->getCrawlerTypeByName($crawler_type_name);
	
		// crawler_url
		$crawler_url = $this->createEmptyCrawlerUrl();
		$crawler_url->crawler_type_id = $crawler_type->id;
		$crawler_url->crawler_host_id = $crawler_host->id;
		$crawler_url->target_id = StreamService::STREAM_TYPE_YOUTUBE . "_stream_" . $stream->id;
		$crawler_url->content_type = '';
		$crawler_url->url = '';
		$crawler_url->last_modified = '';
		$crawler_url->etag = '';
		$crawler_url->file_size = '';
		$crawler_url->title = '';
		$crawler_url->content = '';
		$crawler_url->last_crawled_date = date('Y-m-d H:i:s');
	
		// TODO
		$crawler_url->crawl_interval = 86400;
		$last_crawled_date = strtotime($crawler_url->last_crawled_date);
		$next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);
	
		$crawler_url->next_crawled_date = $next_crawled_date;
		$crawler_url->result = 0;
		$crawler_url->time_out = 0;
		$crawler_url->status_code = 0;
		$this->createCrawlerUrl($crawler_url);
	}

    /**
     * @param $stream
     */
    public function createRssCrawlerUrl($stream) {

        // crawler_host
        $host = SocialApps::getSocialMediaProviderName(SocialApps::PROVIDER_RSS);
        $crawler_host = $this->getCrawlerHostByHost($host);

        // crawler_type
        $crawler_type_name = CrawlerTypes::getCrawlerRssTypeName();
        $crawler_type = $this->getCrawlerTypeByName($crawler_type_name);

        // crawler_url
        $crawler_url = $this->createEmptyCrawlerUrl();
        $crawler_url->crawler_type_id = $crawler_type->id;
        $crawler_url->crawler_host_id = $crawler_host->id;
        $crawler_url->target_id = StreamService::STREAM_TYPE_RSS . "_stream_" . $stream->id;
        $crawler_url->content_type = '';
        $crawler_url->url = '';
        $crawler_url->last_modified = '';
        $crawler_url->etag = '';
        $crawler_url->file_size = '';
        $crawler_url->title = '';
        $crawler_url->content = '';
        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');

        // TODO
        $crawler_url->crawl_interval = 86400;
        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

        $crawler_url->next_crawled_date = $next_crawled_date;
        $crawler_url->result = 0;
        $crawler_url->time_out = 0;
        $crawler_url->status_code = 0;
        $this->createCrawlerUrl($crawler_url);
    }

    public function createInstagramCrawlerUrl($stream) {
        $host = SocialApps::getSocialMediaProviderName(SocialApps::PROVIDER_INSTAGRAM);
        $crawler_host = $this->getCrawlerHostByHost($host);

        $crawler_type_name = CrawlerTypes::getCrawlerInstagramTypeName();
        $crawler_type = $this->getCrawlerTypeByName($crawler_type_name);

        $crawler_url = $this->createEmptyCrawlerUrl();
        $crawler_url->crawler_type_id = $crawler_type->id;
        $crawler_url->crawler_host_id = $crawler_host->id;
        $crawler_url->target_id = StreamService::STREAM_TYPE_INSTAGRAM . "_stream_" . $stream->id;
        $crawler_url->content_type = '';
        $crawler_url->url = '';
        $crawler_url->last_modified = '';
        $crawler_url->etag = '';
        $crawler_url->file_size = '';
        $crawler_url->title = '';
        $crawler_url->content = '';
        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');

        $crawler_url->crawl_interval = 86400;
        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

        $crawler_url->next_crawled_date = $next_crawled_date;
        $crawler_url->result = 0;
        $crawler_url->time_out = 0;
        $crawler_url->status_code = 0;
        $this->createCrawlerUrl($crawler_url);
    }

	/**
	 * @param $target_id
	 * @param int $hidden_flg
	 */
	public function updateHiddenFlgCrawlerUrlByTargetId($target_id, $hidden_flg = 1) {

		$filters = array(
			"target_id" => $target_id,
		);

		$crawlerUrl = $this->crawler_urls->findOne($filters);

		if ($crawlerUrl) {
			$crawlerUrl->hidden_flg = $hidden_flg;
			$this->crawler_urls->save($crawlerUrl);
		}
	}

	public function getCrawlerUrlByTargetId($target_id) {
		$filters = array(
			"target_id" => $target_id,
		);
		return $this->crawler_urls->findOne($filters);
	}

    //crawler_url物理削除
    public function deleteCrawlerUrlByTargetId($target_id) {
        try {
            $crawler_url = $this->getCrawlerUrlByTargetId($target_id);
            $this->crawler_urls->deletePhysical($crawler_url);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->info('deleteCrawlerUrlByTargetId cant delete target_id = '.$target_id);
            throw $e;
        }
    }
}