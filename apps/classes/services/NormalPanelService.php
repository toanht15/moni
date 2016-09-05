<?php
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');
class NormalPanelService extends PanelServiceBase {


	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 */
	public function addEntry($brand, $entry) {

		// パネルに追加
		$panel_name = $this->getNormalPanelName($brand);
		$entry_value = $this->getEntryValueByEntry($entry);

		// あらかじめ削除対象のエントリーを取得しておく（トランザクションをかけるため）
		$hide_entry_values = $this->getHiddenTargetEntriesByNormal($panel_name, 1);

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			$pipe->lPush($panel_name, $entry_value);
			if (count($hide_entry_values) > 0) {
				$pipe->lTrim($panel_name, 0, self::NORMAL_PANEL_MAX_COUNT - 1);
			}

			$this->showAndHideEntries($hide_entry_values, $entry);

			$pipe->exec();

		} catch (Exception $e) {
			$pipe->discard();
			$this->logger->error("NormalPanelService#addEntry Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $next_entry
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 */
	public function moveEntry($brand, $next_entry, $entry) {
		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			$panel_name = $this->getNormalPanelName($brand);

			$entry_value = $this->getEntryValueByEntry($entry);
			$next_entry_value = $this->getEntryValueByEntry($next_entry);

			$pipe->lRem($panel_name, $entry_value, 0);

			$pipe->lInsert($panel_name, Redis::BEFORE, $next_entry_value, $entry_value);

			$pipe->exec();

		} catch (Exception $e) {
			$this->logger->error("NormalPanelService#moveEntry Error");
			$this->logger->error($e);
			$pipe->discard();
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $entry
	 * @throws Exception
	 */
	public function moveToEnd($brand, $entry) {

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			$panel_name = $this->getNormalPanelName($brand);
			$entry_value = $this->getEntryValueByEntry($entry);

			$pipe->lRem($panel_name, $entry_value, 0);

			$pipe->rPush($panel_name, $entry_value);

			$pipe->exec();

		} catch (Exception $e) {
			$this->logger->error("NormalPanelService#moveToEnd Error");
			$this->logger->error($e);
			$pipe->discard();
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 */
	public function deleteEntry($brand, $entry) {
		try {
			$panel_name = $this->getNormalPanelName($brand);
			$entry_value = $this->getEntryValueByEntry($entry);
			$success = $this->redis->lRem($panel_name, $entry_value, 0);
			$this->hideEntry($entry);
            return $success;
		} catch (Exception $e) {
			$this->logger->error("NormalPanelService#deleteEntry Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 */
	public function fixEntry($brand, $entry) {
		throw new Exception ("UnsupportedOperationException");
	}

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 */
	public function unFixEntry($brand, $entry) {
		throw new Exception ("UnsupportedOperationException");
	}

	/**
	 * @param $brand
	 * @param $stream
	 * @param $entry_ids
	 * @return mixed|void
	 * @throws Exception
	 */
	public function addEntriesByStreamAndEntryIds($brand, $stream, $entry_ids) {

		// パネルに追加
		$panel_name = $this->getNormalPanelName($brand);

		// あらかじめ削除対象のエントリーを取得しておく（トランザクションをかけるため）
		$hide_entry_values = $this->getHiddenTargetEntriesByNormal($panel_name, count($entry_ids));

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			foreach ($entry_ids as $entry_id) {
				$entry_value = $this->getEntryValueByStreamAndEntryId($stream, $entry_id);
				$pipe->lPush($panel_name, $entry_value);
			}

			if (count($hide_entry_values)) {
				$pipe->lTrim($panel_name, 0, self::NORMAL_PANEL_MAX_COUNT - 1);
			}

			$this->showAndHideEntries($hide_entry_values);

			$pipe->exec();

            // 対象ブランドのキャッシュを削除する。
            AAFW::import('jp.aainc.classes.CacheManager');
            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($brand->id);

        } catch (Exception $e) {
			$pipe->discard();
			$this->logger->error("PanelService#addEntriesByStreamAndEntryIds Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @return int|mixed
	 * @throws Exception
	 */
	public function count($brand) {
		try {
			$panel_name = $this->getNormalPanelName($brand);
			return $this->redis->lLen($panel_name);
		} catch (Exception $e) {
			$this->logger->error("PanelService#count Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $index
	 * @return mixed|String
	 * @throws Exception
	 */
	public function getEntryByIndex($brand, $index) {
		try {
			$panel_name = $this->getNormalPanelName($brand);
			$value = $this->redis->lIndex($panel_name, $index);
			return $value;
		} catch (Exception $e) {
			$this->logger->error("PanelService#getEntryByIndex Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $offset
	 * @param $limit
	 * @return array|mixed
	 * @throws Exception
	 */
	public function getEntriesByOffsetAndLimit($brand, $offset, $limit) {
		try {
			$panel_name = $this->getNormalPanelName($brand);
			return $this->redis->lRange($panel_name, $offset, $limit);
		} catch (Exception $e) {
			$this->logger->error("NormalPanelService#getEntriesByOffsetAndLimit Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $page
	 * @param $count
	 * @return array|mixed
	 * @throws Exception
	 */
	public function getEntriesByPage($brand, $page, $count = self::DEFAULT_PAGE_COUNT_PC) {
		try {
			$panel_name = $this->getNormalPanelName($brand);
			$offset_and_limit = $this->getOffsetAndLimit($page, $count);
			return $this->redis->lRange($panel_name, $offset_and_limit [0], $offset_and_limit [1]);
		} catch (Exception $e) {
			$this->logger->error("NormalPanelService#getEntriesByPage Error");
			$this->logger->error($e);
			throw $e;
		}
	}


	/**
	 * @param $stream
	 * @throws Exception
	 */
	public function deleteEntriesFromPanelByStream($stream) {

		$entries = array();

		if ($stream->getType() == "facebook") {
			$entries = $this->facebook_stream_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);
		}

		elseif ($stream->getType() == "twitter") {
			$entries = $this->twitter_stream_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);
		}

        elseif ($stream->getType() == "youtube") {
            $entries = $this->youtube_entry_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);
        }

        elseif ($stream->getType() == "rss") {
            $entries = $this->rss_entry_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);
        }

        elseif ($stream->getType() == 'instagram') {
            $entries = $this->instagram_entry_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);
        }

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			$panel_name = $this->getNormalPanelName($stream->getBrand());

			foreach ($entries as $entry) {
				$entry_value = $this->getEntryValueByEntry($entry);
				$pipe->lRem($panel_name, $entry_value, 0);
			}

			$pipe->exec();

		} catch (Exception $e) {
			$pipe->discard();
			$this->logger->error("deleteEntriesByEntries#deleteEntry Error");
			$this->logger->error($e);
			throw $e;
		}
	}
}
