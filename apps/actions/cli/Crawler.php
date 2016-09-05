<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/01/29
 * Time: 12:10
 * To change this template use File | Settings | File Templates.
 */

AAFW::import('jp.aainc.aafw.base.CrawlerBase');

class Crawler extends CrawlerBase {

	public function doService() {

		array_shift($GLOBALS["argv"]);

		foreach ($GLOBALS["argv"] as $crawler_type_name) {

			$this->logger->info("Crawler#doService Start " . "CrawlerType Name=" . $crawler_type_name);

			try {
				$this->processCheck($crawler_type_name);

				$crawler_type = $this->crawler_service->getCrawlerTypeByName($crawler_type_name);

				if ($crawler_type->stop_flg === "0") {
					$this->crawler_service->doCrawl($crawler_type);
				} else {
					$this->logger->info("Crawler#doService crawler_type->stop_flg === 0 " . "CrawlerType Name=" . $crawler_type_name);
				}

			} catch (Exception $e) {
				$this->logger->error("Crawler#doService error" . "CrawlerType Name=" . $crawler_type_name);
				$this->logger->error($e);
			}

			$this->logger->info("Crawler#doService End " . "CrawlerType Name=" . $crawler_type_name);
		}

		return null;
	}
}