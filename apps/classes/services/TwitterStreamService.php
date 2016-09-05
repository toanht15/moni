<?php

AAFW::import('jp.aainc.classes.services.StreamService');

class TwitterStreamService extends StreamService {

    const KIND_USER_TIME_LINE = 1;
    const KIND_SEARCH = 2;

    public function __construct() {
        parent::__construct('Twitter');
    }

    public function isEntryRegistered($stream_id, $object_id) {

        $filter = array(
            "stream_id" => $stream_id,
            "object_id" => $object_id,
        );

        $count = $this->entries->count($filter);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function getKindLabel($kind) {
        return TwitterStreams::getKindLabel($kind);
    }

    public function getTimeLineLabel($kind) {
        return TwitterStreams::getTimeLineLabel($kind);
    }

    public function getHiddenStream($brand_id, $data) {
        $filter = array(
            "brand_id" => $brand_id,
            "brand_social_account_id" => $data['twitter_account'],
            "kind" => $data['twitter_kind'],
            "hidden_flg" => 1,
        );

        if ('2' == $data['twitter_kind']) {
            $filter["keyword"] = $data['twitter_keyword'];
            $filter["timeline"] = 0;
        } else {
            $filter["keyword"] = '';
            $filter["timeline"] = $data['twitter_timeline'];
        }

//        if ('1' == $data['twitter_kind']) {
//            $filter['timeline'] = $data['twitter_timeline'];
//        }

        return $this->streams->findOne($filter);
    }

    public function isStreamRegistered($brand_id, $data) {
        $filter = array(
            "brand_id" => $brand_id,
            "brand_social_account_id" => $data['twitter_account'],
            "kind" => $data['twitter_kind'],
            "hidden_flg" => 0,
        );

        if ('2' == $data['twitter_kind']) {
            $filter["keyword"] = $data['twitter_keyword'];
            $filter["timeline"] = 0;
        } else {
            $filter["keyword"] = '';
            $filter["timeline"] = $data['twitter_timeline'];
        }

        $count = $this->streams->count($filter);

        if ($count > 0) {
            return true;
        }
        return false;
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
            $stream->entry_hidden_flg = $data['entry_hidden_flg'];
            $stream->kind = $data['kind'];
            $stream->hidden_flg = 0;
            $this->createStream($stream);

            // crawler_urlの登録
            $this->crawler_service->createTwitterCrawlerUrl($stream, $brand_social_account);

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#createStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();

        return $stream;
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
            $stream->entry_hidden_flg = $data['entry_hidden_flg'];

            $this->updateStream($stream);
            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("twitter_stream_" . $stream->id, 0);

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#updateStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
        $this->streams->commit();
    }

    /**
     * @param $brand
     * @param $stream_id
     * @return bool|void
     */
    public function delete($brand, $stream_id) {
        $stream = $this->getStreamById($stream_id);
        if ($brand->id == $stream->brand_id) {
            return $this->deleteStream($stream);
        }

        return false;
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

            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("twitter_stream_" . $stream_id, 1);

            $this->brand_social_account_service->updateHiddenFlgBrandSocialAccountByStream($stream,1);

            $this->deleteEntriesFromPanelByStream($stream);

            $this->hideAllEntries($stream->id);

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#hideStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     * @param $entry_id
     * @throws Exception
     */
    public function toggleEntryHiddenFlg($entry_id) {

        $this->streams->begin();

        try {

            $entry = $this->getEntryById($entry_id);
            $entry->hidden_flg = ($entry->hidden_flg) ? 0 : 1;
            $this->updateEntry($entry);

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#toggleEntryHiddenFlg Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     *
     * @param unknown $entry_ids
     * @param unknown $hidden_flg
     * @throws Exception
     */
    public function changeHiddenFlgEntries($entry_ids, $hidden_flg) {

        $this->streams->begin();

        try {

            foreach ($entry_ids as $entry_id) {
                $entry = $this->getEntryById($entry_id);
                if ($entry) {
                    $entry->hidden_flg = $hidden_flg;
                    $this->updateEntry($entry);
                }
            }

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#changeHiddenFlgEntries Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
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
            $this->logger->error("TwitterStreamService#changeEntryHiddenFlgForStream Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function updatePhotoUrl4Entry($data) {

        $this->streams->begin();

        try {

            $entry = $this->getEntryById($data['entry_id']);
            if ($entry) {
                $entry->image_url = $data['image_url'];
                $entry->hidden_flg = $data['hidden_flg'];
                $this->updateEntry($entry);
            }

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#updatePhotoUrl4Entry Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }

    /**
     * @param $entry
     * @param $post
     * @throws Exception
     */
    public function updateEntryByPostObject($entry, $post) {

        $this->streams->begin();

        try {
            if ($post["type"] === StreamService::POST_TYPE_PANEL) {
                $entry = $this->panelToEntry($entry, $post);
            } else {
                $entry->extra_data = json_encode($post);
                $entry->pub_date = date('Y-m-d H:i:s', strtotime($post->created_at));
            }

            $this->updateEntry($entry);

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#updateEntryByPostObject Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }

        $this->streams->commit();
    }
    
    /**
     * 
     * @param unknown $entry
     * @param unknown $response
     * @throws Exception
     */
    public function renewEntry($entry, $response){
        $this->streams->begin();
        try {
            $entry->extra_data = json_encode($response);
            $entry->panel_text = $this->cutLongText($response['text'],200,'...');
            $entry->pub_date = date('Y-m-d H:i:s', strtotime($response['created_at']));
//             $entry->update_date = date('Y-m-d H:i:s', strtotime($response['updated_time'])); ;
            $this->updateEntry($entry);
        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#renewEntry Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
    
        $this->streams->commit();
    }
    /**
     * @param $entry
     * @param $post
     * @return mixed
     */
    private function panelToEntry($entry, $post) {
        $entry->target_type = $post['target_type'] ? $post['target_type'] : TwitterEntry::TARGET_TYPE_NORMAL;
        return $entry;
    }

    /**
     * @param TwitterStream $stream
     * @param CrawlerUrl $crawler_url
     * @param $response
     * @param string $display_order
     */
    public function doStore(TwitterStream $stream, CrawlerUrl $crawler_url, $response, $display_order = 'updated_at') {

        try {

            $this->streams->begin();

            // エラーがあった場合
            if (isset($response["errors"])) {

                $http_status = "200";
                $errors_message = "Error";
                if (isset($response["httpstatus"])) {
                    $http_status = $response["httpstatus"];
                }
                if (isset($response["errors"][0])) {
                    $errors_message = $response["errors"][0]["message"] . "_" . $response["errors"][0]["code"];
                }
                $this->updateCrawlerUrlForError($crawler_url, $http_status, $errors_message);
                $this->streams->commit();
                return;

            } else if (isset($response["httpstatus"])) {
                if ($response["httpstatus"] != 200) {
                    $http_status = $response["httpstatus"];
                    $this->updateCrawlerUrlForError($crawler_url, $http_status, "Response is not valid status");
                    $this->streams->commit();
                    return;
                }
            }

            // 検索じゃない場合
            $next_url = "";

            if ($stream->kind == "1") {

                $http_status = 200;
                if (isset($response["httpstatus"])) {
                    $http_status = $response["httpstatus"];
                    unset($response["httpstatus"]);
                }
                $statuses = $response;

                if (count($statuses) > 0) {
                    $last_status = array_shift($statuses);
                    $next_url = "?since_id=" . $last_status["id_str"];
                    $statuses[] = $last_status;
                }
            } else {
                $statuses = $response["statuses"];
                $http_status = $response["httpstatus"];
                //$search_metadata = $response["search_metadata"];
                //$next_url = $search_metadata["refresh_url"];
                if (count($statuses) > 0) {
                    $last_status = array_shift($statuses);
                    $next_url = "?since_id=" . $last_status["id_str"];
                    $statuses[] = $last_status;
                }
            }

            $this->updateCrawlerUrl($crawler_url, $http_status, $next_url);

            $entry_ids = $this->createEntries($stream, $statuses);

            if ($stream->entry_hidden_flg === "0") {
                if (count($entry_ids) > 0) {
                    $normal_panel_service = $this->service_factory->create("NormalPanelService");
                    $normal_panel_service->addEntriesByStreamAndEntryIds($stream->getBrand(), $stream, $entry_ids);
                }
            }

            $this->streams->commit();

            $display_limit = $this->brand_social_account_service->getBrandSocialAccountById($stream->brand_social_account_id)->display_panel_limit;
            if (!$this->streams->entry_hidden_flg && $display_limit) {
                $this->filterPanelByLimit($stream, $display_limit, $display_order);
            }

        } catch (Exception $e) {
            $this->logger->error("TwitterStreamService#doStore Error");
            $this->logger->error($e);
            $this->streams->rollback();
        }
    }

    /**
     * @param TwitterStream $stream
     * @param $statuses
     * @return array
     */
    private function createEntries(TwitterStream $stream, $statuses) {

        $entry_ids = array();

        foreach ($statuses as $status) {

            if ($this->isEntryRegistered($stream->id, $status["id_str"])) {
                continue;
            }

            $entry = $this->createEmptyEntry();
            $entry->stream_id = $stream->id;
            $entry->brand_id = $stream->brand_id;
            $entry->object_id = $status["id_str"];
            $entry->panel_text = $this->cutLongText($status["text"],200,'...');
            $entry->link = $this->getSnsUrl() . $status["user"]["screen_name"] . "/status/" . $status["id_str"];
            $entry->creator_id = $status["user"]["id"];
            $entry->pub_date = date('Y-m-d H:i:s', strtotime($status["created_at"]));
            $entry->update_date = date('Y-m-d H:i:s', strtotime($status["created_at"]));

            // 画像ほ保存するように修正
            if (isset($status["entities"])) {
                if (isset($status["entities"]["media"])) {
                    if (isset($status["entities"]["media"][0])) {
                        if ($status["entities"]["media"][0]["media_url_https"]) {
                            $entry->image_url = $status["entities"]["media"][0]["media_url_https"];
                        } else {
                            $entry->image_url = $status["entities"]["media"][0]["media_url"];
                        }

                    }
                }
            }

            $entry->extra_data = json_encode($status);

            //search panel image
            if(!$entry->image_url) {
                // パネルテキストがなければ初期値をセット
                if($entry->detail_data) {
                    $detail_data = json_decode($entry->detail_data);
                    usort($detail_data->images, function($a, $b){
                        if ($a->height == $b->height) {
                            return 0;
                        }
                        return ($a->height > $b->height) ? -1 : 1;
                    });
                    $entry->image_url = $detail_data->images[0]->source;
                } elseif($entry->extra_data) {
                    $extra_data = json_decode($entry->extra_data);
                    $entry->image_url = $extra_data->picture;
                }
            }

            if ($stream->entry_hidden_flg == "1") {
                $entry->hidden_flg = 1;
            } else {
                $entry->hidden_flg = 0;
            }

            if ($entry->link) {
                $this->createEntry($entry);
                $entry_ids[] = $entry->id;
            }
        }

        return $entry_ids;
    }

    /**
     * @param CrawlerUrl $crawler_url
     * @param $http_status
     * @param $next_url
     */
    private function updateCrawlerUrl(CrawlerUrl $crawler_url, $http_status, $next_url) {

        if ($next_url != "") {
            $crawler_url->url = $next_url;
        }

        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');
        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $crawler_url->next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);
        $crawler_url->result = 0;
        $crawler_url->time_out = 0;
        $crawler_url->status_code = $http_status;
        $crawler_url->errors_count = 0;
        $crawler_url->errors_message = "";

        $this->crawler_service->updateCrawlerUrl($crawler_url);
    }

    /**
     * @param CrawlerUrl $crawler_url
     * @param $http_status
     * @param $errors_message
     */
    private function updateCrawlerUrlForError(CrawlerUrl $crawler_url, $http_status, $errors_message) {
        $crawler_url->last_crawled_date = date('Y-m-d H:i:s');
        $last_crawled_date = strtotime($crawler_url->last_crawled_date);
        $crawler_url->next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);
        $crawler_url->result = "";
        $crawler_url->time_out = "";
        $crawler_url->status_code = $http_status;
        $crawler_url->errors_count = $crawler_url->errors_count + 1;
        $crawler_url->errors_message = $errors_message;
        $this->crawler_service->updateCrawlerUrl($crawler_url);
    }
}