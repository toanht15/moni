<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/01/29
 * Time: 10:16
 * To change this template use File | Settings | File Templates.
 */


AAFW::import('jp.aainc.classes.task.ICrawlerTask');

abstract class CrawlerTask implements ICrawlerTask {

	protected $service_factory;
	protected $crawler_type;
	protected $crawler_urls;
	protected $stream_service;
	protected $crawler_service;
	protected $brand_social_account_service;
	protected $logger;

	abstract function prepare();

	abstract function crawl();

	abstract function finish();

	public function doExecute() {
		$this->prepare();
		$this->crawl();
		$this->finish();
	}

	protected function logError($msg, $e) {
		aafwLog4phpLogger::getDefaultLogger()->error($msg, $e);
		aafwLog4phpLogger::getHipChatLogger()->error($msg, $e);
	}
}