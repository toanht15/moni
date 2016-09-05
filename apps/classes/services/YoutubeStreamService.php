<?php

AAFW::import('jp.aainc.classes.services.StreamService');

class YoutubeStreamService extends StreamService {

	const KIND_USER_TIME_LINE = 1;

	public function __construct() {
		parent::__construct('Youtube');
	}
	/**
	 * @param unknown $brand
	 * @param unknown $brand_social_account
	 * @param unknown $data
	 * @throws Exception
	 * @return unknown
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
			$this->crawler_service->createYoutubeCrawlerUrl($stream, $brand_social_account);

		} catch (Exception $e) {
			$this->logger->error("YoutubeStreamService#createStreamAndCrawlerUrl Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();

		return $stream;
	}

    /**
     * @param $stream
     * @param $crawler_url
     * @param $playlistItemsResponse
     * @param string $display_order
     */
    public function doStore($stream, $crawler_url, $playlistItemsResponse, $display_order = 'updated_at'){
		try {
			$this->streams->begin();
			$entry_ids = $this->createEntries($stream,$playlistItemsResponse);

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
			$this->logger->error("YoutubeStreamService#doStore Error");
			$this->logger->error($e);
			$this->streams->rollback();
		}
	}

	/**
	 * @param unknown $stream
	 * @param unknown $playlistItemsResponse
	 * @return multitype:NULL
	 */
	public function createEntries($stream, $playlistItemsResponse){
		$entry_ids = array();

		foreach ($playlistItemsResponse as $playlistItem) {

			if ($this->isEntryRegistered($stream->id, $playlistItem['snippet']['resourceId']['videoId'])) {
				continue;
			}

			$entry = $this->createEmptyEntry();
			$entry->stream_id = $stream->id;
			$entry->brand_id = $stream->brand_id;
			$entry->object_id = $playlistItem['snippet']['resourceId']['videoId'];
			$entry->panel_text = $this->cutLongText($playlistItem['snippet']['description'],200,'...');
			$entry->link = $this->getSnsUrl() . "watch?v=" . $entry->object_id;
			$entry->image_url = $playlistItem['snippet']['thumbnails']['medium']['url'] ?: $playlistItem['snippet']['thumbnails']['default']['url'];
			$entry->extra_data = json_encode($playlistItem);
			$entry->pub_date = date('Y-m-d H:i:s', strtotime( $playlistItem['snippet']['publishedAt']));
			$entry->updated_at = date('Y-m-d H:i:s',time());
			$entry->created_at = date('Y-m-d H:i:s',time());
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
	 * @param $stream_id
	 * @param $video_id
	 * @return bool
	 */
	public function isEntryRegistered($stream_id, $video_id) {
		$filter = array(
			"stream_id" => $stream_id,
			"object_id" => $video_id
		);

		$count = $this->entries->count($filter);
		if ($count > 0) {
			return true;
		}
		return false;
	}

	/**
	 * @param $entry
	 * @param $videoItem
	 */
	public function renewEntry($entry, $videoItem){
		$entry->object_id = $videoItem['items'][0]['id'];
		$entry->link = $this->getSnsUrl() . "watch?v=" . $entry->object_id;
		$entry->image_url = $videoItem['items'][0]['snippet']['thumbnails']['medium']['url'] ?: $videoItem['items'][0]['snippet']['thumbnails']['default']['url'];
		$entry->panel_text = $this->cutLongText($videoItem['items'][0]['snippet']['description'],200,'...');
        $entry->extra_data = json_encode($videoItem['items'][0]);
		$entry->updated_at = date('Y-m-d H:i:s',time());
		$this->updateEntry($entry);
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
			$this->logger->error("YoutubeStreamService#changeEntryHiddenFlgForStream Error");
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

			$this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("youtube_stream_" . $stream_id, 1);

            $this->brand_social_account_service->updateHiddenFlgBrandSocialAccountByStream($stream,1);

			$this->deleteEntriesFromPanelByStream($stream);

            $this->hideAllEntries($stream->id);

		} catch (Exception $e) {
			$this->logger->error("YoutubeStreamService#hideStreamAndCrawlerUrl Error");
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
				$entry->panel_text = $post->text;
				$entry->extra_data = json_encode($post);
				$entry->pub_date = date('Y-m-d H:i:s', strtotime($post->created_at));
			}

			$this->updateEntry($entry);

		} catch (Exception $e) {
			$this->logger->error("YoutubeStreamService#updateEntryByPostObject Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}

		$this->streams->commit();
	}

	private function panelToEntry($entry, $post) {
		$entry->panel_text = $post["panel_text"];
        $entry->target_type = $post['target_type'] ? $post['target_type'] : YoutubeEntry::TARGET_TYPE_NORMAL;

		return $entry;
	}

    public function updateStreamAndCrawlerUrl($stream, $data) {

        $this->streams->begin();

        try {
            $stream->hidden_flg = 0;
            $stream->entry_hidden_flg = $data['entry_hidden_flg'];

            $this->updateStream($stream);
            $this->crawler_service->updateHiddenFlgCrawlerUrlByTargetId("youtube_stream_" . $stream->id, 0);

        } catch (Exception $e) {
            $this->logger->error("YoutubeStreamService#updateStreamAndCrawlerUrl Error");
            $this->logger->error($e);
            $this->streams->rollback();
            throw $e;
        }
        $this->streams->commit();
    }

    public function getYoutubeVideoInfo($channel, Google_YouTubeService $youtubeService) {
        $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

        $playlistItemsStatus = $youtubeService->playlistItems->listPlaylistItems('status', array(
            'playlistId' => $uploadsListId,
            'maxResults' => 50
        ));

        $private_playlist = array();
        foreach ($playlistItemsStatus['items'] as $playlistItemStatus) {
            if ($playlistItemStatus['status']['privacyStatus'] === 'private') {
                $private_playlist[] = $playlistItemStatus['id'];
            }
        }

        $playlistItemsResponse = $youtubeService->playlistItems->listPlaylistItems('snippet', array(
            'playlistId' => $uploadsListId,
            'maxResults' => 50
        ));

        $playlistItems = array();
        foreach ($playlistItemsResponse['items'] as $playlistItem) {
            if (!in_array($playlistItem['id'],$private_playlist)) {
                $playlistItems[] = $playlistItem;
            }
        }
        return $playlistItems;
    }
}
