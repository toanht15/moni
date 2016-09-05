<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.validator.FileValidator');

class FacebookStreamService extends StreamService {

	const DETAIL_DATA_UPDATE_MAX_ERROR_COUNT = 3;
	const KIND_NEWS_FEED = 0;
	const DEFAULT_VALUE = ' ';

	const KIND_USER_TIME_LINE = 1;
	const KIND_SEARCH = 2;


	const IMAGE_WIDTH = 640;
	const IMAGE_HEIGHT = 640;

    const PANEL_MAX_LENGTH = 200;

	public function __construct() {
		parent::__construct('Facebook');

	}

	/**
	 *
	 * @param unknown $stream_id
	 * @param unknown $post_id
	 * @return boolean
	 */
	public function isEntryRegistered($stream_id, $post_id) {
		$filter = array(
			"stream_id" => $stream_id,
			"post_id" => $post_id
		);

		$count = $this->entries->countOnMaster($filter);
		if ($count > 0) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param unknown $kind
	 */
	public function getStreamKindName($kind) {
		return $this->streams->getStreamKindName($kind);
	}

	/**
	 *
	 * @param unknown $kind
	 */
	public function getTimeLineLabel($kind) {
		return $this->streams->getTimeLineLabel($kind);
	}

	/**
	 *
	 * @param unknown $brand_id
	 * @param unknown $data
	 * @return boolean
	 */
	public function isStreamRegistered($brand_id, $data) {
		$filter = array(
			"brand_id" => $brand_id,
			"brand_social_account_id" => $data ['fb_account'],
			"kind" => 1,
			"timeline" => $data ['fb_timeline'],
			"hidden_flg" => '0'
		);

		$count = $this->streams->count($filter);

		if ($count > 0) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param unknown $brand_id
	 * @param unknown $data
	 */
	public function getHiddenStream($brand_id, $data) {
		$filter = array(
			"brand_id" => $brand_id,
			"brand_social_account_id" => $data ['fb_account'],
			"kind" => 1,
			"timeline" => $data ['fb_timeline'],
			"hidden_flg" => '1'
		);

		return $this->streams->findOne($filter);
	}

	/**
	 *
	 * @param unknown $streamId
	 */
	public function getStreamByStreamId($streamId){
		$filter = array(
				"id" => $streamId,
		);
		return $this->streams->findOne($filter);
	}

	/**
	 *
	 * @param
	 *            $brand
	 * @param
	 *            $brand_social_account
	 * @param
	 *            $data
	 * @return mixed
	 * @throws Exception
	 */
	public function createStreamAndCrawlerUrl($brand, $brand_social_account, $data) {
		$this->streams->begin();

		try {
			$stream = $this->createEmptyStream();
			$stream->brand_id = $brand->id;
			$stream->brand_social_account_id = $brand_social_account->id;
			$stream->kind = $data ['kind'];
			$stream->entry_hidden_flg = $data ['entry_hidden_flg'];
			$stream->entry_detail_update_flg = 1;
			$stream->hidden_flg = 0;
			$this->createStream($stream);

			// crawler_urlの登録
			$this->crawler_service->createFacebookCrawlerUrl($stream);
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#createStreamAndCrawlerUrl error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();

		return $stream;
	}

	/**
	 *
	 * @param
	 *            $stream
	 * @param
	 *            $data
	 * @throws Exception
	 */
	public function updateStreamAndCrawlerUrl($stream, $data) {
		$this->streams->begin();

		try {
			$stream->hidden_flg = 0;
			$stream->entry_hidden_flg = $data ['entry_hidden_flg'];
			$this->updateStream($stream);

			// crawler_urlを更新
			$this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("facebook_stream_" . $stream->id, 0);
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#updateStreamAndCrawlerUrl error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	/**
	 *
	 * @param
	 *            $brand
	 * @param
	 *            $stream_id
	 * @return bool void
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

			$this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("facebook_stream_" . $stream_id, 1);

            $this->brand_social_account_service->updateHiddenFlgBrandSocialAccountByStream($stream,1);

			$this->deleteEntriesFromPanelByStream($stream);

            $this->hideAllEntries($stream->id);

		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#hideStreamAndCrawlerUrl Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	/**
	 *
	 * @param
	 *            $entry_id
	 * @throws Exception
	 */
	public function toggleEntryHiddenFlg($entry_id) {
		$this->streams->begin();

		try {

			$entry = $this->getEntryById($entry_id);
			$entry->hidden_flg = ($entry->hidden_flg) ? 0 : 1;
			$this->updateEntry($entry);
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#toggleEntryHiddenFlg Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	/**
	 *
	 * @param
	 *            $entry_id
	 * @param
	 *            $hidden_flg
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
			$this->logger->error("FacebookStreamService#changeHiddenFlgEntries Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}


	/**
	 *
	 * @param unknown $entry_id
	 * @throws Exception
	 */
	public function toggleEntryCommentHiddenFlg($entry_id) {
		$this->streams->begin();

		try {

			$entry = $this->getEntryById($entry_id);
			$entry->comment_hidden_flg = ($entry->comment_hidden_flg) ? 0 : 1;
			$this->updateEntry($entry);
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#toggleEntryCommentHiddenFlg Error");
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
			$this->logger->error("FacebookStreamService#changeEntryHiddenFlgForStream Error");
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
			$entry = $this->getEntryById($data ['entry_id']);
			if ($entry) {
				$entry->panel_text = $this->cutLongText($data ['text'], self::PANEL_MAX_LENGTH,'...');
				$entry->image_url = $data ['image_url'];
				$entry->hidden_flg = $data ['hidden_flg'];
				$this->updateEntry($entry);
			}
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#updatePhotoUrl4Entry Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	public function updateEntryByPostObject($entry, $post) {
		try {

			$this->streams->begin();

			if ($post ["type"] === "status") {
				$entry = $this->statusToEntry($entry, $post);
			} else if ($post ["type"] === "link") {
				$entry = $this->linkToEntry($entry, $post);
			} else if ($post ["type"] === "question") {
				$entry = $this->QuestionToEntry($entry, $post);
			} else if ($post['type'] === FacebookEntry::ENTRY_TYPE_PHOTO) {
                $entry = $this->photoToEntry($entry, $post);
            } else if ($post ["type"] === StreamService::POST_TYPE_PANEL) {
				$entry = $this->panelToEntry($entry, $post);
			} else {
				$entry->post_id = $post ["id"];
				$entry->type = $post ["type"];
				$entry->object_id = $post ["object_id"];
				$entry->panel_text = $this->cutLongText($post ["message"],self::PANEL_MAX_LENGTH,'...');
				$entry->target_type = $post ["target_type"] ? $post ["target_type"] : FacebookEntry::TARGET_TYPE_NORMAL;
				$entry->creator_id = $post ["from"]->id;
				$entry->extra_data = json_encode($post);
				$entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
				$entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
			}

			$this->updateEntry($entry);

			$this->streams->commit();

		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#updateEntryByPostObject Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}
	}

    /**
     * @param FacebookStream $stream
     * @param CrawlerUrl $crawler_url
     * @param $response
     * @param string $display_order
     */
    public function doStore(FacebookStream $stream, CrawlerUrl $crawler_url, $response, $display_order = 'updated_at') {
        for($i=0; $i < count($response['data']); $i++){
            $response['data'][$i] = (array)$response['data'][$i];
        }
        $response ["paging"] = (array)$response ["paging"];

		try {

			$this->streams->begin();

			$this->updateStream($stream);

			$entry_ids = $this->createEntries($stream, $response);
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
			$this->logger->error("FacebookStreamService#doStore Error");
			$this->logger->error($e);
			$this->streams->rollback();
		}
	}

	/**
	 * @param FacebookStream $stream
	 * @param $response
	 * @return array
	 */
	private function createEntries(FacebookStream $stream, $response) {
		$entry_ids = array();

		$brand_social_account = $stream->getBrandSocialAccount();

        foreach ($response ["data"] as $post) {

			// 投稿を限定する（ユーザーの投稿を保存しない）
			if ($post["from"]->id != $brand_social_account->social_media_account_id) {
				continue;
			}

			if ($this->isEntryRegistered($stream->id, $post["id"])) {
				continue;
			}

			$entry = $this->createEmptyEntry();
			$entry->stream_id = $stream->id;
			$entry->brand_id = $stream->brand_id;
            $entry->status_type = $post["status_type"];

			if ($post["type"] === FacebookEntry::ENTRY_TYPE_STATUS) {
                $entry = $this->statusToEntry($entry, $post);
			} else if ($post["type"] === FacebookEntry::ENTRY_TYPE_LINK) {
                $entry = $this->linkToEntry($entry, $post);
            } else if ($post['type'] === FacebookEntry::ENTRY_TYPE_PHOTO) {
                $entry = $this->photoToEntry($entry, $post);
            } else if ($post["type"] === "question") {
				continue;
			} else {
				$entry->post_id = $post ["id"];
				$entry->type = $post ["type"];
				$entry->panel_text = $this->cutLongText($post ["message"],self::PANEL_MAX_LENGTH,'...');
				$entry->link = $post ["link"];
				$entry->creator_id = $post ["from"]->id;

				if (isset($post["object_id"])) {
					$entry->object_id = $post ["object_id"];
				} elseif ($entry->creator_id) {
                    $ids = explode($entry->creator_id . "_", $entry->post_id);
                    if (count($ids) >= 2) {
                        $entry->object_id = $ids [1];
                    }
				}
				$entry->extra_data = json_encode($post);
				$entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
				$entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
			}

			if ($stream->entry_hidden_flg == "1") {
				$entry->hidden_flg = 1;
			} else {
				$entry->hidden_flg = 0;
			}

			if ($entry->detail_data == '') {
				$entry->detail_data = self::DEFAULT_VALUE;
			}

            //search panel image
            if(!$entry->image_url) {
                // パネルテキストがなければ初期値をセット
                if($entry->detail_data && ($entry->detail_data != self::DEFAULT_VALUE)) {
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

            if (!$entry->panel_text) {
                $entry->panel_text = $this->cutLongText($post['description'], self::PANEL_MAX_LENGTH, '...');
            }

			//link default value
			if ($entry->link == '')
				$entry->link = self::DEFAULT_VALUE;
			// text emoji
			$entry->panel_text = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $entry->panel_text);

            if ($entry->object_id && $entry->creator_id) {
                $this->createEntry($entry);
                $entry_ids [] = $entry->id;
            }

		}

		return $entry_ids;
	}

	/**
	 * @param $entry
	 * @param $post
	 * @return mixed
	 */
	private function statusToEntry($entry, $post) {
		$entry->post_id = $post ["id"];
		$entry->type = $post ["type"];
		$entry->creator_id = $post ["from"]->id;

		$object_id = 0;
		$link = "";
		if (isset($post["object_id"])) {
            $object_id = $post ["object_id"];
        } elseif ($entry->creator_id) {
            $ids = explode($entry->creator_id . "_", $entry->post_id);
            if (count($ids) >= 2) {
                $object_id = $ids [1];
                $link = $this->getSnsUrl() . $entry->creator_id . "/posts/" . $object_id;
            }
        }

		$entry->object_id = $object_id;
		$entry->link = $link;
		$entry->panel_text = $this->cutLongText($post ["message"],self::PANEL_MAX_LENGTH,'...');


		$entry->extra_data = json_encode($post);
		$entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
		$entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
		return $entry;
	}

	private function linkToEntry($entry, $post) {
		$entry->post_id = $post ["id"];
        $entry->type = $post ["type"];
		$entry->creator_id = $post ["from"]->id;

		$link = "";
		$object_id = 0;
		if (isset($post["object_id"])) {
            $object_id = $post ["object_id"];
        } elseif ($entry->creator_id) {
            $ids = explode($entry->creator_id . "_", $entry->post_id);
            if (count($ids) >= 2) {
                $object_id = $ids [1];
                $link = $this->getSnsUrl() . $entry->creator_id . "/posts/" . $object_id;
            }
        }

		$entry->object_id = $object_id;
		$entry->link = $link;

		$entry->panel_text = $this->cutLongText($post ["message"],self::PANEL_MAX_LENGTH,'...');
		if (!$entry->panel_text) {
			$entry->panel_text = $this->cutLongText($post ["story"],self::PANEL_MAX_LENGTH,'...');
		}

		$entry->extra_data = json_encode($post);
		$entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
		$entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
		return $entry;
	}


    /**
     * @param $entry
     * @param $post
     * @return mixed
     */
    private function photoToEntry($entry, $post) {
        $entry->post_id = $post ["id"];
        $entry->type = $post ["type"];
        $entry->creator_id = $post ["from"]->id;

        $link = "";
        $object_id = 0;

        if (isset ($post ["object_id"])) {
            $object_id = $post ["object_id"];
        } elseif ($entry->creator_id) {
            $ids = explode($entry->creator_id . "_", $entry->post_id);
            if (count($ids) >= 2) {
                $object_id = $ids [1];
                $link = $this->getSnsUrl() . $entry->creator_id . "/posts/" . $object_id;
            }
        }

        $entry->object_id = $object_id;
        $entry->link = $link != "" ? $link : $post['link'];

        $entry->panel_text = $this->cutLongText($post ["message"],self::PANEL_MAX_LENGTH,'...');
        $entry->extra_data = json_encode($post);
        $entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
        $entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
        return $entry;
    }

	/**
	 * @param $entry
	 * @param $post
	 * @return mixed
	 */
	private function QuestionToEntry($entry, $post) {
		$entry->post_id = $post ["id"];
		$entry->type = $post ["type"];
		$entry->creator_id = $post ["from"]->id;
		$entry->object_id = $post ["object_id"];

		// linkの取得
		if (count($post ["actions"]) > 0) {
			$entry->link = $post ["actions"] [0] ["link"];
		}

		$entry->panel_text = $this->cutLongText($post ["story"],self::PANEL_MAX_LENGTH,'...');
		$entry->extra_data = json_encode($post);
		$entry->pub_date = date('Y-m-d H:i:s', strtotime($post ["created_time"]));
		$entry->update_date = date('Y-m-d H:i:s', strtotime($post ["updated_time"]));
		return $entry;
	}

	private function panelToEntry($entry, $post) {
		$entry->panel_text = $post ["panel_text"];
        $entry->target_type = $post['target_type'] ? $post['target_type'] : FacebookEntry::TARGET_TYPE_NORMAL;
		return $entry;
	}

	/**
	 * @param CrawlerUrl $crawler_url
	 * @param $response
	 */
	private function updateCrawlerUrl(CrawlerUrl $crawler_url, $response) {
		$paging = $response ["paging"];

		$crawler_url->content_type = "";
		// $crawler_url->last_modified = date('Y-m-d H:i:s', strtotime($rss->last_modified));;
		// $crawler_url->etag = "";
		// $crawler_url->file_size = "";

		if (isset ($paging ["previous"])) {
            $url_params = explode("?", $paging ["previous"]);
            $url_params = explode("&", $url_params[1]);
            $params = '';
            foreach($url_params as $url_param) {
                if(preg_match('/access_token/',$url_param)) continue;
                $params .= $url_param.'&';
            }
			$crawler_url->url = substr_replace($params ,"",-1);
            ;
		}

		// $crawler_url->content = "";
		$crawler_url->last_crawled_date = date('Y-m-d H:i:s');

		$last_crawled_date = strtotime($crawler_url->last_crawled_date);
		$next_crawled_date = date("Y/m/d H:i:s", $last_crawled_date + $crawler_url->crawl_interval);

		$crawler_url->next_crawled_date = $next_crawled_date;
		$crawler_url->result = 0;
		$crawler_url->time_out = 0;
		$this->crawler_service->updateCrawlerUrl($crawler_url);
	}

	public function getStreamsForUpdateComment() {
		$filter = array(
			'conditions' => array(
				"hidden_flg" => 0,
				"comment_update_flg" => 1
			)
		);

		return $this->streams->find($filter);
	}

	public function getEntriesCountForUpdateComment($stream_id) {
		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
				"comment_update_limit_date:>=" => date('Y-m-d H:i:s')
			)
		);
		return $this->entries->count($filter);
	}

	public function getEntriesForUpdateComment($stream_id) {
		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
				"comment_update_limit_date:>=" => date('Y-m-d H:i:s')
			)
		);
		return $this->entries->find($filter, false, true);
	}

	public function updateComment($entry, $comments) {
		$this->streams->begin();

		try {
			$extra_data = json_decode($entry->extra_data);

			if (count($comments ["data"]) > 0) {

				$result = $this->isIncludeNgWordOfComments($comments ["data"]);
				if ($result) {
					$entry->include_ng_comment_flg = 1;
					$entry->comment_hidden_flg = 1;
				} else {
					$entry->include_ng_comment_flg = 0;
					$entry->comment_hidden_flg = 0;
				}

				$extra_data->comments = $comments;
			} else {
				unset ($extra_data->comments);
			}
			$entry->extra_data = json_encode($extra_data);
			$this->updateEntry($entry);
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#updateComment Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	public function getStreamsForUpdateDetail() {
		$filter = array(
			'conditions' => array(
				"hidden_flg" => 0,
				"entry_detail_update_flg" => 1
			)
		);

		return $this->streams->find($filter);
	}

    public function getStreamsForUpdateDetailByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                "hidden_flg" => 0,
                "entry_detail_update_flg" => 1,
                "brand_id" => $brand_id
            )
        );

        return $this->streams->find($filter);
    }

	public function getEntriesForUpdateDetail($stream_id) {
		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
				"object_id:!=" => "0",
				"detail_data_update_flg" => "0",
				"detail_data_update_error_count:<" => self::DETAIL_DATA_UPDATE_MAX_ERROR_COUNT,
				"type" => array(
					'photo',
					'link',
					'video'
				)
			)
		);
		return $this->entries->find($filter, false, true);
	}

	/**
	 * @param $response
	 * @return mixed
	 */
	public function getImageUrl($response) {
        $response = (object)$response;
		$image_url = "";
		$images = $response->images;

		if ($response->picture) {
			$image_url = $response->picture;
		}

		foreach ($images as $image) {
			if ($image->width <= self::IMAGE_WIDTH || $image->height <= self::IMAGE_HEIGHT) {
				$image_url = $image->source;
				break;
			}
		}

		//facebookのsafe_image.phpだったらリンクを分ける
		if (preg_match('/safe_image.php\?/', $image_url)) {
			$url_parts = parse_url($image_url); //URLを分ける
			parse_str($url_parts['query'], $url_query);

			//facebook stagingの画像の場合は、リンクを分けない
			if(preg_match('/^fbstaging/', $url_query['url'])) {
				return FacebookEntry::FACEBOOK_STAGING;
			}

			$image_url = $url_query['url'] ? urldecode($url_query['url']) : $image_url;
		}

		return $image_url;
	}

	/**
	 * @param $entry
	 * @param $response
	 * @param $save_image
	 * @throws Exception
	 */
	public function updateDetail($entry, $response, $save_image = false) {

		$this->streams->begin();

		try {
			$entry->detail_data = json_encode($response);

			if ($save_image) {
				$entry = $this->saveImageToStorage($entry, $response);
			} else {
				$entry->image_url = $this->getImageUrl($response) !== FacebookEntry::FACEBOOK_STAGING ?: null;
				$entry->detail_data_update_flg = 1;
			}

			if ($entry->detail_data_update_error_count >= FacebookStreamService::DETAIL_DATA_UPDATE_MAX_ERROR_COUNT) {
				$this->logger->error('FacebookStreamService#updateDetail() update facebook entries detail error 3 times! entry_id='.$entry->id);
				$hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
				$hipchat_logger->error('FacebookStreamService#updateDetail() update facebook entries detail error 3 times! entry_id='.$entry->id);
			}

			$this->updateEntry($entry);

            $this->streams->commit();
		} catch (Exception $e) {
			$this->logger->error("FacebookStreamService#updateDetail Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}
	}

    /**
     *
     * @param $entry
     * @param $response
     * @return mixed
     */
    public function saveImageToStorage($entry, $response) {
        $url = $this->getImageUrl($response);

		//画像がない場合は
		if(!$url || $url == FacebookEntry::FACEBOOK_STAGING) {
			$entry->detail_data_update_flg = 1;
			return $entry;
		}

		//画像をS3にアップロードする
		$storage_url = $this->uploadImage($entry, $url);

		//画像をアップロード失敗の場合は
		if(!$storage_url) {
			$entry->detail_data_update_error_count += 1;
			$entry->detail_data = null;
		} else {
			$entry->detail_data_update_flg = 1;
			$entry->image_url = $storage_url;
		}

		return $entry;
	}

    /**
     * @param $entry
     * @param $url
     * @return mixed|string
     */
    public function uploadImage($entry, $url) {
        $file_info['name'] = '/tmp/' . uniqid();

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.104 Safari/537.36',
            )
        ));

        // ファイル保存
        file_put_contents($file_info['name'], file_get_contents($url, false, $context));

        $file_validator = new FileValidator($file_info, FileValidator::FILE_TYPE_IMAGE);

		//ファイルのバリデータ
		if (!$file_validator->validateFacebookEntryImage()) {
			unlink($file_info['name']);
			$this->logger->error('FacebookStreamService#uploadImage: upload image failed! file invalid! entry_id = '.$entry->id);
			return null;
		}

		//file_info生成される
        $result = StorageClient::getInstance()->putObject(StorageClient::toHash('brand/' . $entry->getFacebookStream()->brand_id . '/facebook_entry/' . StorageClient::getUniqueId()), $file_validator->getFileInfo(), StorageClient::ACL_PUBLIC_READ, false);

        unlink($file_info['name']);

        return $result;
    }

    /**
     *
     * @param unknown $entry
     * @param unknown $response
     * @throws Exception
     */
    public function renewEntry($entry, $response){
        try {
            $this->streams->begin();

            $entry->detail_data = json_encode($response);
            $entry->detail_data_update_flg = 1;
            $entry->image_url = $this->getImageUrl($response);

			if($entry->type === FacebookEntry::ENTRY_TYPE_LINK && $entry->image_url == FacebookEntry::FACEBOOK_STAGING) {
				$entry->image_url = $response['picture'];
			}

            if ($entry->type === FacebookEntry::ENTRY_TYPE_PHOTO && $response['from']->id && $response['id']) {
                $entry->link = $this->getSnsUrl() . $response['from']->id . "/posts/" . $response['id'];
            } else {
                $entry->link = $response['link'];
            }

            if ($response['message']) {
                $entry->panel_text = $this->cutLongText($response['message'],self::PANEL_MAX_LENGTH,'...');
            } else if ($response['description']) {
                $entry->panel_text = $this->cutLongText($response['description'],self::PANEL_MAX_LENGTH,'...');
            }else if ($response['name']) {
                $entry->panel_text = $this->cutLongText($response['name'], self::PANEL_MAX_LENGTH, '...');
            }else {
                $entry->panel_text = "";
            }
            if ($response['created_time']) {
                $entry->pub_date = date('Y-m-d H:i:s', strtotime($response['created_time']));
            }
            if ($response['updated_time']) {
                $entry->update_date = date('Y-m-d H:i:s', strtotime($response['updated_time'])); ;
            }
            if ($response['status_type']) {
                $entry->status_type = $response['status_type'];
            }
            $this->updateEntry($entry);

            $this->streams->commit();
        } catch (Exception $e) {
            $this->logger->error("FacebookStreamService#renewEntry Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
    }

	/**
	 * @param $objectID
	 * @param $streamID
	 * @return mixed
	 */
	public function getEntryByObjectID($objectID, $streamID) {
		$filter = array(
			"object_id" => $objectID,
			"stream_id" => $streamID
		);
		return $this->entries->findOne($filter);
	}

    public function getEntryByPostId($post_id, $stream_id) {
        return $this->entries->findOne(array("post_id" => $post_id, "stream_id" => $stream_id));
    }

}
