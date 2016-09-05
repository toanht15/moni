<?php

AAFW::import('jp.aainc.classes.task.CrawlerTask');

require_once('vendor/magpierss-0.72/rss_fetch.inc');

define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

class RssFetchTask extends CrawlerTask {

    public function __construct($crawler_type) {
        $this->service_factory = new aafwServiceFactory ();
        $this->crawler_type = $crawler_type;
        $this->crawler_service = $this->service_factory->create("CrawlerService");
        $this->stream_service = $this->service_factory->create("RssStreamService");
        $this->crawler_urls = $this->crawler_service->getAvailableCrawlerUrlsByCrawlerTypeId($this->crawler_type->id);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }


    public function prepare() {
        $this->crawler_type->process_urls = count($this->crawler_urls);
        $this->crawler_service->updateCrawlerType($this->crawler_type);
    }


    public function crawl() {

        foreach ($this->crawler_urls as $crawler_url) {

            try {

                $target_object = explode("rss_stream_", $crawler_url->target_id);
                $stream = $this->stream_service->getStreamById($target_object[1]);
                $rss = fetch_rss($stream->rss_url);

                $rss->image['url'] = $this->stream_service->getImageUrl($rss->image['url'],$rss->channel["link"]);
                $i = 0;
                foreach ($rss->items as $item) {
                    $rss->items[$i++]['image_url'] = $this->stream_service->imageSearch($item["description"],$item["link"]);
                }

                $this->stream_service->doStore($stream, $rss, $crawler_url);

            } catch (Exception $e) {
                $this->logError("RssFetchTask#crawl() Exception crawler_url_id = " . $crawler_url->id, $e);
            }
        }

    }

    public function finish() {
        $this->crawler_type->process_urls = 0;
        $this->crawler_type->last_crawled_date = date('Y-m-d H:i:s');
        $this->crawler_service->updateCrawlerType($this->crawler_type);
    }
}