<?php

AAFW::import('jp.aainc.aafw.base.aafwActionBase');
AAFW::import('jp.aainc.aafw.factory.aafwEntityStoreFactory');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');

abstract class CrawlerBase extends aafwActionBase {

	protected $logger;

	protected $crawler_service;
	protected $twitter_stream_service;
	protected $facebook_stream_service;

	protected $_ModelDefinitions = array(
		'Brands',
		'BrandSocialAccounts',
		'TwitterStreams',
		'FacebookStreams',
		'CrawlerHosts',
		'CrawlerTypes',
		'CrawlerUrls',
	);

	public function __construct() {
		parent::__construct();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->initModel();
		$this->initService();
	}

	public function initModel() {
		foreach ($this->getModelDefinitions() as $class) {
			$this->setModel($class, aafwEntityStoreFactory::create($class));
		}
	}

	public function initService() {
		$this->setServiceFactory(new aafwServiceFactory ());
		$this->crawler_service = $this->createService('CrawlerService');
		$this->twitter_stream_service = $this->createService('TwitterStreamService');
		$this->facebook_stream_service = $this->createService('FacebookStreamService');
	}

	public function validate() {
		if (php_sapi_name() != 'cli') return false;
		return true;
	}

	public function processCheck($crawler_type_name) {
		$cmd = 'ps ax | grep "' . $crawler_type_name . '" | grep -v grep | wc -l';
		$ret = shell_exec($cmd);
		if ($ret > 2) {
			$this->logger->error("Crawler#doService processCheckError() " . "CrawlerType Name=" . $crawler_type_name . " Process Count=" . $ret);
			exit;
		}
	}
}