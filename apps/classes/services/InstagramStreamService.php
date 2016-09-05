<?php
AAFW::import('jp.aainc.classes.services.StreamService');

class InstagramStreamService extends StreamService {
    const KIND_RECENT_MEDIA = 1;

    const CAPTION_MAX_LENGTH = 200;

    public function __construct() {
        parent::__construct('Instagram');
    }

    /**
     * @param $brand
     * @param $brand_social_account
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function createStreamAndCrawlerUrl($brand, $brand_social_account, $data) {
        $this->streams->begin();

        try {
            $stream = $this->createEmptyStream();
            $stream->brand_id = $brand->id;
            $stream->brand_social_account_id = $brand_social_account->id;
            $stream->kind = $data['kind'];
            $stream->entry_hidden_flg = $data ['entry_hidden_flg'];
            $stream->entry_detail_update_flg = 1;
            $stream->hidden_flg = 0;
            $this->createStream($stream);

            // register for crawler_url
            $this->crawler_service->createInstagramCrawlerUrl($stream);
        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#createStreamAndCrawlerUrl error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
        return $stream;
    }

    /**
     * @param InstagramStream $stream
     * @param CrawlerUrl $crawler_url
     * @param $response
     * @param string $display_order
     */
    public function doStore(InstagramStream $stream, CrawlerUrl $crawler_url, $response, $display_order = 'updated_at', $init_flg = false) {
        try {
            $this->streams->begin();

            $this->updateStream($stream);

            $entry_ids = $this->createEntries($stream, $response, $init_flg);
            if ($stream->entry_hidden_flg === "0") {
                if (count($entry_ids) > 0) {
                    $normal_panel_service = $this->service_factory->create("NormalPanelService");
                    $normal_panel_service->addEntriesByStreamAndEntryIds($stream->getBrand(), $stream, $entry_ids);
                }
            }

            $this->updateCrawlerUrl($crawler_url, $response);

            $this->streams->commit();

            $display_limit = $this->brand_social_account_service->getBrandSocialAccountById($stream->brand_social_account_id)->display_panel_limit;
            if (!$this->streams->entry_hidden_flg && $display_limit) {
                $this->filterPanelByLimit($stream, $display_limit, $display_order);
            }
        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#doStore Error");
            $this->logger->error($e);
            $this->streams->rollback();
        }
    }

    public function changeEntryHiddenFlgForStream($stream_id, $entry_hidden_flg) {
        $this->streams->begin();

        try {
            $stream = $this->getStreamById($stream_id);
            $stream->entry_hidden_flg = $entry_hidden_flg;
            $this->updateStream($stream);
        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#changeEntryHiddenFlgForStream Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     * @param InstagramStream $stream
     * @param $response
     * @return array
     */
    public function createEntries(InstagramStream $stream, $response, $init_flg) {
        $entry_ids = array();

        if (is_array($response->data)) {
            foreach ($response->data as $media) {
                if ($this->isEntryRegistered($stream->id, $media->id)) continue;

                if ($init_flg) {
                    $instagram_entry_count = $this->getEntriesCountByStreamIds($stream->id);

                    if ($instagram_entry_count >= InstagramEntry::INIT_CRAWL_COUNT) {
                        break;
                    }
                }

                $entry = $this->createEmptyEntry();

                $entry->stream_id = $stream->id;
                $entry->hidden_flg = $stream->entry_hidden_flg === '1' ? 1 : 0;

                $entry = $this->parseMediaToEntry($entry, $media);

                $this->createEntry($entry);
                $entry_ids[] = $entry->id;
            }
        }

        return $entry_ids;
    }

    /**
     * @param $entry
     * @param $post
     * @throws Exception
     */
    public function updateEntryByPostObject($entry, $post) {
        try {
            $this->streams->begin();

            if ($post['type'] === StreamService::POST_TYPE_PANEL) {
                $entry = $this->parsePanelToEntry($entry, $post);
            }

            $this->updateEntry($entry);

            $this->streams->commit();

        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#updateEntryByPostObject Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
    }

    /**
     * @param $entry
     * @param $response
     * @throws Exception
     */
    public function renewEntry($entry, $response){
        try {
            $this->streams->begin();

            $entry = $this->parseMediaToEntry($entry, $response->data);

            $this->updateEntry($entry);

            $this->streams->commit();
        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#renewEntry Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
    }

    /**
     * @param $crawler_url
     * @param $response
     */
    public function updateCrawlerUrl($crawler_url, $response) {
        list($media) = $response->data;

        $crawler_url->url = 'min_id=' . $media->id;
        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');

        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

        $crawler_url->next_crawled_date = $next_crawled_date;
        $crawler_url->result = 0;
        $crawler_url->time_out = 0;

        $this->crawler_service->updateCrawlerUrl($crawler_url);
    }

    /**
     * @param $stream
     * @param $data
     * @throws Exception
     */
    public function updateStreamAndCrawlerUrl($stream, $data) {
        $this->streams->begin();

        try {
            $stream->hidden_flg = 0;
            $stream->entry_hidden_flg = $data ['entry_hidden_flg'];
            $this->updateStream($stream);

            // crawler_urlを更新
            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("instagram_stream_" . $stream->id, 0);
        } catch (Exception $e) {
            $this->logger->error("InstagramStreamService#updateStreamAndCrawlerUrl error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
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

            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("instagram_stream_" . $stream_id, 1);

            $this->brand_social_account_service->updateHiddenFlgBrandSocialAccountByStream($stream,1);

            $this->deleteEntriesFromPanelByStream($stream);

            $this->hideAllEntries($stream->id);

            $this->streams->commit();
        } catch (Exception $e) {
            $this->logger->error("FacebookStreamService#hideStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
    }

    /**
     * @param $entry
     * @param $post
     * @return mixed
     */
    public function parsePanelToEntry($entry, $post) {
        $entry->panel_comment = $post ["panel_comment"];
        $entry->link = $post['link'];
        return $entry;
    }

    /**
     * @param $entry
     * @param $media
     * @return mixed
     */
    public function parseMediaToEntry($entry, $media) {
        $entry->object_id = $media->id;
        $entry->type = $media->type;
        $entry->link = $media->link;
        $entry->filter = $media->filter;
        $entry->image_url = $media->images->standard_resolution->url;

        $entry->pub_date = date("Y-m-d H:i:s", $media->created_time);
        $entry->extra_data = json_encode($media);
        $entry->panel_text = $this->cutLongText($media->caption->text, self::CAPTION_MAX_LENGTH, '...');

        return $entry;
    }

    /**
     * @param $stream_id
     * @param $post_id
     * @return bool
     */
    public function isEntryRegistered($stream_id, $post_id) {
        $filter = array(
            "stream_id" => $stream_id,
            "object_id" => $post_id
        );
        $count = $this->entries->count($filter);

        return $count > 0;
    }
}
