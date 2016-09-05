<?php

AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');

class TopPanelService extends PanelServiceBase {

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed|void
	 * @throws Exception
	 * トップパネル：非表示→表示
	 */
	public function addEntry($brand, $entry) {

		$entry_value = $this->getEntryValueByEntry($entry);
		$top_panel_name = $this->getTopPanelName($brand);
		$normal_panel_name = $this->getNormalPanelName($brand);

		// 削除対象を取得しておく
		$hidden_target_entries_top = $this->getHiddenTargetEntriesByTop($top_panel_name, 1);

		// 削除対象がある場合
		$hidden_target_entries_normal = array();
		if ($hidden_target_entries_top) {
			// Normalパネルの数を確認しておく
			$hidden_target_entries_normal = $this->getHiddenTargetEntriesByNormal($normal_panel_name, 1);
		}

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			// トップパネルに追加
			$pipe->lPush($top_panel_name, $entry_value);

			if (count($hidden_target_entries_top) > 0) {

				// NORMALパネルに移動
				$pipe->rPop($top_panel_name);
				$pipe->lPush($normal_panel_name, $entry_value);

				// NORMALパネルの件数チェック
				if (count($hidden_target_entries_normal) > 0) {
					$pipe->lTrim($normal_panel_name, 0, self::NORMAL_PANEL_MAX_COUNT - 1);
				}

				$this->showAndHideEntries($hidden_target_entries_normal, $entry);
			}

			$pipe->exec();

		} catch (Exception $e) {
			$pipe->discard();
			$this->logger->error("TopPanelService#addEntry Error");
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

			$panel_name = $this->getTopPanelName($brand);

			$entry_value = $this->getEntryValueByEntry($entry);
			$next_entry_value = $this->getEntryValueByEntry($next_entry);

			$pipe->lRem($panel_name, $entry_value, 0);

			$pipe->lInsert($panel_name, Redis::BEFORE, $next_entry_value, $entry_value);

			$pipe->exec();

		} catch (Exception $e) {
			$this->logger->error("TopPanelService#moveEntry Error");
			$this->logger->error($e);
			$pipe->discard();
			throw $e;
		}
	}

	/**
	 *
	 * @param unknown $brand
	 * @param unknown $entry
	 * @throws Exception
	 */
	public function moveToEnd($brand, $entry) {

		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {

			$panel_name = $this->getTopPanelName($brand);

			$entry_value = $this->getEntryValueByEntry($entry);

			$pipe->lRem($panel_name, $entry_value, 0);

			$pipe->rPush($panel_name, $entry_value);

			$pipe->exec();

		} catch (Exception $e) {
			$this->logger->error("TopPanelService#moveToEnd Error");
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

			$top_panel_name = $this->getTopPanelName($brand);
			$entry_value = $this->getEntryValueByEntry($entry);

			// TOPパネルから削除
			$success = $this->redis->lRem($top_panel_name, $entry_value, 0);
			$this->hideEntry($entry);
            return $success;
		} catch (Exception $e) {
			$this->logger->error("TopPanelService#deleteEntry Error");
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

		try {
			//データベースの操作
			$this->changePriority($entry,'1');

			$entry_value = $this->getEntryValueByEntry($entry);

			// Normalパネルから削除
			$normal_panel_name = $this->getNormalPanelName($brand);
			$this->redis->lRem($normal_panel_name, $entry_value, 0);

			// TOPパネルに追加
			$top_panel_name = $this->getTopPanelName($brand);
			$this->redis->lPush($top_panel_name, $entry_value);

			// TOPパネルの件数チェック
			$count = $this->redis->lLen($top_panel_name);
			if ($count > self::TOP_PANEL_MAX_COUNT) {

				// NORMALパネルに移動
				$move_entry_value = $this->redis->rPop($top_panel_name);
				$move_entry = $this->getEntryByEntryValue($move_entry_value);
				$this->changePriority($move_entry,'0');
				
				$this->redis->lPush($normal_panel_name, $move_entry_value);

				// NORMALパネルの件数チェック
				$count = $this->redis->lLen($normal_panel_name);
				if ($count > self::NORMAL_PANEL_MAX_COUNT) {
					$hide_entry_values = $this->redis->lRange($normal_panel_name, self::NORMAL_PANEL_MAX_COUNT, $count - 1);
					$this->redis->lTrim($normal_panel_name, 0, self::NORMAL_PANEL_MAX_COUNT - 1);
					$this->showAndHideEntries($hide_entry_values);
				}
			}

		} catch (Exception $e) {
			$this->logger->error("TopPanelService#fixEntry Error");
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
	public function unFixEntry($brand, $entry) {

		$entry_value = $this->getEntryValueByEntry($entry);
		$top_panel_name = $this->getTopPanelName($brand);
		$normal_panel_name = $this->getNormalPanelName($brand);
		
		// 削除対象を取得しておく
		$hidden_target_entries = $this->getHiddenTargetEntriesByNormal($normal_panel_name, 1);


		$pipe = $this->redis->multi(Redis::PIPELINE);

		try {
			//データベースの操作
			$this->changePriority($entry,'0');
			
			// TOPパネルから削除
			$pipe->lRem($top_panel_name, $entry_value, 0);

			// NORMALパネルに追加
			$pipe->lPush($normal_panel_name, $entry_value);

			if (count($hidden_target_entries) > 0) {
				$pipe->lTrim($normal_panel_name, 0, self::NORMAL_PANEL_MAX_COUNT - 1);
				$this->showAndHideEntries($hidden_target_entries);
			}

			$pipe->exec();

		} catch (Exception $e) {
			$pipe->discard();
			$this->logger->error("TopPanelService#unFixEntry Error");
			$this->logger->error($e);
			throw $e;
		}
	}

	/**
	 * @param $brand
	 * @param $stream
	 * @param $entry_ids
	 * @return mixed|void
	 * @throws Exception
	 */
	public function addEntriesByStreamAndEntryIds($brand, $stream, $entry_ids) {
		throw new Exception ("UnsupportedOperationException");
	}


	/**
	 * @param $brand
	 * @return int|mixed
	 * @throws Exception
	 */
	public function count($brand) {
		try {
			$panel_name = $this->getTopPanelName($brand);
			return $this->redis->lLen($panel_name);

		} catch (Exception $e) {
			$this->logger->error("TopPanelService#count Error");
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
			$panel_name = $this->getTopPanelName($brand);
			$value = $this->redis->lIndex($panel_name, $index);
			return $value;

		} catch (Exception $e) {
			$this->logger->error("TopPanelService#getEntryByIndex Error");
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
			$panel_name = $this->getTopPanelName($brand);
			return $this->redis->lRange($panel_name, $offset, $limit);
		} catch (Exception $e) {
			$this->logger->error("TopPanelService#getEntriesByOffsetAndLimit Error");
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
			$panel_name = $this->getTopPanelName($brand);
			$offset_and_limit = $this->getOffsetAndLimit($page, $count);
			return $this->redis->lRange($panel_name, $offset_and_limit[0], $offset_and_limit[1]);
		} catch (Exception $e) {
			$this->logger->error("TopPanelService#getEntriesByPage Error");
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

			$panel_name = $this->getTopPanelName($stream->getBrand());

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
