<?php
AAFW::import('jp.aainc.classes.services.StreamService');
require_once('vendor/magpierss-0.72/rss_fetch.inc');
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
class RssStreamService extends StreamService {

    public function __construct() {
        parent::__construct('Rss');
    }

    /**
     * @param $url
     * @return bool|int|MagpieRSS|mixed
     */
    public function fetch_rss($url)
    {
        return fetch_rss($url);
    }

    /**
     * @param $brand
     * @param $rss
     * @param $rss_url
     * @return mixed
     * @throws Exception
     */
    public function createAndUpdateStreamAndCrawlerUrl($brand, $rss, $rss_url) {
        /** @var BrandSocialAccountService $brand_social_service */
        $brand_social_service = $this->service_factory->create('BrandSocialAccountService');

        try {
            //check if stream already existed
            $this->streams->begin();

            $stream = $this->getStreamByLink($brand->id, $rss->channel['link']);
            if($stream){
                $stream->title = $rss->channel["title"];
                $stream->link = $rss->channel["link"];
                $stream->description = $rss->channel["description"];
                $stream->language = $rss->channel["language"];
                $stream->image_url =  $rss->image['url'];
                $stream->rss_url = $rss_url;
                $stream->entry_hidden_flg = 1;
                if ($stream->hidden_flg == 1) {
                    $stream->order_no = $brand_social_service->getMaxOrder($brand->id) + 1;
                }
                $stream->hidden_flg = 0;
                $this->updateStream($stream);
                $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("rss_stream_" . $stream->id, 0);
            }else{
                    $stream = $this->createEmptyStream();
                    $stream->brand_id = $brand->id;
                    $stream = $this->rssToStream($stream, $rss);
                    $stream->rss_url = $rss_url;
                    $stream->image_url =  $rss->image['url'];
                    $stream->entry_hidden_flg = 1;
                    $stream->order_no = $brand_social_service->getMaxOrder($brand->id) + 1;
                    $stream->hidden_flg = 0;
                    $this->createStream($stream);

                    // crawler_urlの登録
                    $this->crawler_service->createRssCrawlerUrl($stream);
            }

            $this->streams->commit();
        } catch (Exception $e) {
            $this->logger->error("RssStreamService#createAndUpdateStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        return $stream;
    }

    public function getImageUrl($image_url, $source_url){
        if($image_url){
           return $image_url;
        }else{
            return $this->imageSearch(null,$source_url);
        }
    }

    /**
     * @param $stream
     * @param $rss
     * @param $crawler_url
     * @param string $display_order
     */
    public function doStore($stream, $rss, $crawler_url, $display_order='updated_at') {

        try {

            $this->streams->begin();

            $stream = $this->rssToStream($stream, $rss);

            $stream->image_url = $rss->image['url'];

            $this->updateStream($stream);

            $entry_ids = $this->createEntries($stream, $rss);

            $this->updateCrawlerUrl($crawler_url, $rss);

            $this->streams->commit();

            if ($stream->entry_hidden_flg === "0") {
                if (count($entry_ids) > 0) {
                    $normal_panel_service = $this->service_factory->create("NormalPanelService");
                    $normal_panel_service->addEntriesByStreamAndEntryIds($stream->getBrand(), $stream, $entry_ids);
                }
            }

            if (!$this->streams->entry_hidden_flg && $stream->display_panel_limit) {
                $this->filterPanelByLimit($stream, $stream->display_panel_limit, $display_order);
            }

        } catch (Exception $e) {
            $this->logger->error("RssStreamService#updateStreamAndCreateEntriesByRss Error " . $e);
            $this->streams->rollback();
        }
    }

    /**
     * @param $stream
     * @param $rss
     * @return mixed
     */
    private function rssToStream($stream, $rss) {

        $stream->title = $rss->channel["title"];
        $stream->link = $rss->channel["link"];
        $stream->description = $rss->channel["description"];
        $stream->language = $rss->channel["language"];
        $stream->image_url = $rss->image['url'];

        return $stream;
    }

    /**
     * @param $stream
     * @param $rss
     * @return array
     */
    private function createEntries($stream, $rss) {
        $entry_ids = array();
        foreach ($rss->items as $item) {
            if ($this->isEntryRegistered($stream->id, $item["link"])) {
                continue;
            }else{
                $entry = $this->itemToEntry($stream, $rss, $item);
                if ($entry->link) {
                    $this->createEntry($entry);
                    $entry_ids [] = $entry->id;
                }
            }
        }
        return $entry_ids;
    }

    /**
     * @param $stream_id
     * @param $link
     * @return bool
     */
    public function isEntryRegistered($stream_id, $link) {

        $filter = array(
            "stream_id" => $stream_id,
            "link" => $link,
        );

        if($this->entries->findOne($filter)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $stream
     * @param $rss
     * @param $item
     * @return mixed
     */
    private function itemToEntry($stream, $rss, $item) {

        $entry = $this->createEmptyEntry();
        $entry->stream_id = $stream->id;
        $entry->author = $item['author_name'];
        $entry->image_url = $item["image_url"];

        if (strtolower($rss->feed_type) == "atom") {
            $entry->text = $item["title"];
            $entry->panel_text = $this->cutLongText($item["title"],200,'...');
            $entry->description = $item["atom_content"]?$item["atom_content"]:'';
            $entry->link = $item["link"];
            $entry->pub_date = date('Y-m-d H:i:s', strtotime($item["published"]));
            $entry->update_date = date('Y-m-d H:i:s', strtotime($item["updated"]));
        } else {
            $entry->text = $item["title"];
            $entry->panel_text = $this->cutLongText($item["title"],200,'...');
            $entry->description = $item["description"]?$item["description"]:'';
            $entry->link = $item["link"];

            if (isset($item["dc"]["date"])) {
                $entry->pub_date = date('Y-m-d H:i:s', strtotime($item["dc"]["date"]));
                $entry->update_date = date('Y-m-d H:i:s', strtotime($item["dc"]["date"]));
            } elseif(isset($item["pubdate"])){
                $entry->pub_date = date('Y-m-d H:i:s', strtotime($item["pubdate"]));
                $entry->update_date = date('Y-m-d H:i:s', strtotime($item["pubdate"]));
            }
        }
        if($entry->link){
            $link_array = preg_split("/http:\/\//",$entry->link);
            if(count($link_array) > 1){
                $entry->link = 'http://'.end($link_array);
            }
        }
        if ($stream->entry_hidden_flg == "1") {
            $entry->hidden_flg = 1;
        } else {
            $entry->hidden_flg = 0;
        }
        return $entry;
    }

    /**
     * @param CrawlerUrl $crawler_url
     * @param $rss
     */
    private function updateCrawlerUrl(CrawlerUrl $crawler_url, $rss) {

        $crawler_url->content_type = "";
        if($rss->last_modified) {
            $crawler_url->last_modified = date('Y-m-d H:i:s', strtotime($rss->last_modified));
        }
        $crawler_url->etag = $rss->etag;
        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');

        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

        $crawler_url->next_crawled_date = $next_crawled_date;
        $this->crawler_service->updateCrawlerUrl($crawler_url);
    }

    /**
     * @param $brandId
     * @param $link
     * @return mixed
     */
    public function getStreamByLink($brandId, $link){
        $filter = array(
            "brand_id" => $brandId,
            "link" => $link
        );
        return $this->streams->findOne($filter);
    }

    /**
     * @param $streamId
     * @param $link
     * @return mixed
     */
    public function getEntryByStreamIdAndPageUrl($streamId, $link){
        $filter = array(
            "stream_id" => $streamId,
            "link" => $link
        );
        return $this->entries->findOne($filter);
    }

    /**
     * @param $description
     * @param $link
     * @return null|string
     */
   public function imageSearch($description, $link){
        try {
            if ($html = @DOMDocument::loadHTML($description)) {
                $xpath = new DOMXPath($html);
                $imageNodes = $xpath->query("//img");
                if ($imageNodes->item(0)) {
                    return $imageNodes->item(0)->getAttribute('src');
                }
            }

            if ($html = @DOMDocument::loadHTML(file_get_contents($link))) {
                $xpath = new DOMXPath($html);
                $imageNodes = $xpath->query("//head/meta[@content][@name='msapplication-TileImage']/@content");
                if ($imageNodes->item(0)) {
                    return $imageNodes->item(0)->nodeValue;
                }
                $imageNodes = $xpath->query("//head/meta[@content][@property='og:image']/@content");
                if ($imageNodes->item(0)) {
                    return $imageNodes->item(0)->nodeValue;
                }
            }

        }catch (Exception $e){
            $this->logger->error("RssStreamService#imageSearch Error");
            $this->logger->error($e);
        }
        return null;
    }

    /**
     * @param $brandId
     * @return mixed
     */
    public function getStreamByBrandId($brandId){
        $filter = array(
            "brand_id" => $brandId,
            "hidden_flg" => "0"
        );
        return $this->streams->find($filter);
    }

    /**
     * @param $stream_id
     * @throws Exception
     */
    public function hideStreamAndCrawlerUrl($stream_id) {

        $this->streams->begin();

        try {
            $stream = $this->getStreamById($stream_id);
            if ($stream) {
                $stream->hidden_flg = 1;
                $this->updateStream($stream);
            }

            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("rss_stream_" . $stream_id, 1);

            $this->deleteEntriesFromPanelByStream($stream);

            $this->hideAllEntries($stream->id);

        } catch (Exception $e) {
            $this->logger->error("RssStreamService#hideStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     * @param $stream
     */
    protected function deleteEntriesFromPanelByStream($stream) {

        /**@var $normal_panel_service NormalPanelService */
        $normal_panel_service = $this->service_factory->create("NormalPanelService");
        $normal_panel_service->deleteEntriesFromPanelByStream($stream);

        /**@var $top_panel_service TopPanelService */
        $top_panel_service = $this->service_factory->create("TopPanelService");
        $top_panel_service->deleteEntriesFromPanelByStream($stream);

    }

    /**
     * @param $stream_id
     * @param $entry_hidden_flg
     * @throws Exception
     */
    public function changeEntryHiddenFlgForStream($stream_id, $entry_hidden_flg) {
        $this->streams->begin();

        try {

            $stream = $this->getStreamById($stream_id);
            $stream->entry_hidden_flg = $entry_hidden_flg;
            $this->updateStream($stream);
        } catch (Exception $e) {
            $this->logger->error("RssStreamService#changeEntryHiddenFlgForStream Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    public function count($streamId){
        $filter = array(
            "stream_id" => $streamId
        );
        return $this->entries->count($filter);
    }

    /**
     * @param $streamId
     * @param int $display_panel_limit
     */
    public function updateDisplayPanelLimit($streamId, $display_panel_limit = 0) {
        $stream = $this->getStreamById($streamId);
        $stream->display_panel_limit = $display_panel_limit;
        $this->streams->save($stream);
    }

    /**
     * @param $streamId
     * @param $order
     */
    public function updateOrder($streamId, $order) {
        $stream = $this->getStreamById($streamId);
        $stream->order_no = $order;
        $this->streams->save($stream);
    }

    public function getMaxOrder($brand_id) {
        $filter = array(
            'conditions'=> array(
                "brand_id" => $brand_id,
                "hidden_flg" => 0
            )
        );
        return $this->streams->getMax('order_no', $filter);
    }

    public function getDisconnectedRssOverMonth() {
        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $filter = array(
            'conditions' => array(
                'hidden_flg' => 1,
                'updated_at:<' => $date->format('Y-m-d H:i:s')
            )
        );
        return $this->streams->find($filter);
    }

    public function deleteDisconnectedRssOverMonth() {
        /** @var CrawlerService $crawler_service */
        $crawler_service = $this->service_factory->create('CrawlerService');

        $need_delete_streams = $this->getDisconnectedRssOverMonth();
        $stream_id = '';
        try {
            foreach ($need_delete_streams as $stream) {
                $this->streams->begin();

                $stream_id = $stream->id;
                $entries = $stream->getRssEntries();
                foreach($entries as $entry) {
                    $this->entries->deletePhysical($entry);
                }
                $this->streams->deletePhysical($stream);
                $crawler_service->deleteCrawlerUrlByTargetId('rss_stream_'.$stream->id);

                $this->streams->commit();
                $this->logger->info('RssStreamService deleteDisconnectedRssOverMonth deleted stream_id = '.$stream->id);
            }
        } catch (Exception $e) {
            $this->streams->rollback();
            $this->logger->error('RssStreamService deleteDisconnectedRssOverMonth cant delete stream_id = '.$stream_id);
            $this->logger->error($e);
        }
    }
}
