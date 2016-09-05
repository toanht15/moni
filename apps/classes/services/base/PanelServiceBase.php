<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.services.base.PanelService');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

abstract class PanelServiceBase extends aafwServiceBase implements PanelService {

	const NORMAL_PANEL = "normal_panel_";
	const TOP_PANEL    = "top_panel_";

	const DELIMITER    = ":";

    const ENTRY_DISPLAY_TYPE_LARGE = '3';
    const ENTRY_DISPLAY_TYPE_MIDDLE = '2';
    const ENTRY_DISPLAY_TYPE_SMALL = '1';

	const TOP_PANEL_MAX_COUNT    = 3;
	const NORMAL_PANEL_MAX_COUNT = 1000;
	const DEFAULT_PAGE_COUNT_PC  = 20;
    const DEFAULT_PAGE_COUNT_SP  = 10;
    const INSTAGRAM_PAGE_LIMIT_PC  = 18;

    public static $panel_name = array(
        'normal' => self::NORMAL_PANEL,
        'top'    => self::TOP_PANEL
    );

	protected $logger;

	/** @var $redis Redis */
	protected $redis;

	public function __construct() {
		$this->transaction = $this->getModel("Brands");
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
		$this->redis = $this->createRedis();
		$this->service_factory = new aafwServiceFactory ();
		$this->twitter_stream_service = $this->service_factory->create("TwitterStreamService");
		$this->facebook_stream_service = $this->service_factory->create("FacebookStreamService");
		$this->static_html_entry_service = $this->service_factory->create("StaticHtmlEntryService");
		$this->link_entry_service = $this->service_factory->create("LinkEntryService");
		$this->youtube_entry_service = $this->service_factory->create("YoutubeStreamService");
        $this->rss_entry_service = $this->service_factory->create("RssStreamService");
        $this->instagram_entry_service = $this->service_factory->create('InstagramStreamService');
        $this->photo_stream_service = $this->service_factory->create('PhotoStreamService');
        $this->page_stream_service = $this->service_factory->create('PageStreamService');
        $this->cp_instagram_hashtag_stream_service = $this->service_factory->create('CpInstagramHashtagStreamService');
	}

	public function __destruct() {
		$this->redis->close();
		unset($this->redis);
	}

	/**
	 * @param $brand
	 * @param $panel
	 * @return string
	 */
	protected function getPanelName($brand, $panel) {
        return self::$panel_name[$panel] . $brand->id;
	}

	/**
	 * @param $brand
	 * @return mixed
	 */
	protected function getNormalPanelName($brand) {
		return $this->getPanelName($brand, "normal");
	}

	/**
	 * @param $brand
	 * @return mixed
	 */
	protected function getTopPanelName($brand) {
		return $this->getPanelName($brand, "top");
	}

	/**
	 * @param $entry
	 * @return string
	 */
	protected function getEntryValueByEntry($entry) {
        try {
            return $entry->getEntryPrefix() . ":" . $entry->id;
        } catch(Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e);
            return '';
        }
	}
    
	/**
	 * @param $stream
	 * @param $entry_id
	 * @return string
	 */
	protected function getEntryValueByStreamAndEntryId($stream, $entry_id) {
		return $stream->getEntryPrefix() . ":" . $entry_id;
	}

	/**
	 * @param $entry_value
	 * @return mixed
	 */
	public function getEntryByEntryValue($entry_value) {

		$result = explode(self::DELIMITER, $entry_value);

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_TWITTER) {
			return $this->twitter_stream_service->getEntryById($result[1]);
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_FACEBOOK) {
			return $this->facebook_stream_service->getEntryById($result[1]);
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_STATIC_HTML) {
			return $this->static_html_entry_service->getEntryById($result[1]);
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_LINK) {
			return $this->link_entry_service->getEntryById($result[1]);
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_YOUTUBE) {
			return $this->youtube_entry_service->getEntryById($result[1]);
		}

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_RSS) {
            return $this->rss_entry_service->getEntryById($result[1]);
        }

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_INSTAGRAM) {
            return $this->instagram_entry_service->getEntryById($result[1]);
        }

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_PHOTO) {
            return $this->photo_stream_service->getEntryById($result[1]);
        }

        if ($result[0] == IPanelEntry::ENTRY_PREFIX_PAGE) {
            return $this->page_stream_service->getEntryById($result[1]);
        }

        if ($result[0] == IPanelEntry::ENTRY_PREFIX_CP_INSTAGRAM_HASHTAG) {
            return $this->cp_instagram_hashtag_stream_service->getEntryById($result[1]);
        }
	}

	/**
	 *
	 * @param unknown $entry_value
	 * @return string
	 */
	public function getStreamNameByEntryValue($entry_value) {

		$result = explode(self::DELIMITER, $entry_value);
		if ($result[0] === IPanelEntry::ENTRY_PREFIX_TWITTER) {
			return 'TwitterStream';
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_FACEBOOK) {
			return 'FacebookStream';
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_STATIC_HTML) {
			return 'StaticHtmlEntry';
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_LINK) {
			return 'LinkEntry';
		}

		if ($result[0] === IPanelEntry::ENTRY_PREFIX_YOUTUBE) {
			return 'YoutubeStream';
		}

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_RSS) {
            return 'RssStream';
        }

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_INSTAGRAM) {
            return 'InstagramStream';
        }

        if ($result[0] === IPanelEntry::ENTRY_PREFIX_PHOTO) {
            return 'PhotoStream';
        }

        if ($result[0] == IPanelEntry::ENTRY_PREFIX_PAGE) {
            return 'PageStream';
        }

        if ($result[0] == IPanelEntry::ENTRY_PREFIX_CP_INSTAGRAM_HASHTAG) {
            return 'CpInstagramHashtagStream';
        }
	}

	/**
	 * @param $entry_values
	 * @return mixed
	 */
	protected function getEntriesByEntryValues($entry_values) {
		$entries = array();
		foreach ($entry_values as $entry_value) {
			$entry = $this->getEntryByEntryValue($entry_value);
			if ($entry) {
				$entries[] = $entry;
			}
		}
		return $entries;
	}


	/**
	 * @param $page
	 * @param $count
	 * @return array
	 */
	protected function getOffsetAndLimit($page, $count) {

		if ($page === 1) {
			$offset = 0;
			$limit = $count - 1;
		} else {
			$offset = $count * ($page - 1);
			$limit = $count - 1;
		}
		$result = array($offset, $limit);
		return $result;
	}

	/**
	 * @param $entry_values
	 */
	protected function hideEntries($entry_values) {
		$entries = $this->getEntriesByEntryValues($entry_values);
		foreach ($entries as $entry) {
			$entry->hidden_flg = 1;
			$entry->priority_flg = 0;
			$entries = $this->getModel($entry->getStoreName());
			$entries->save($entry);
		}
	}

	/**
	 * @param $entry
	 */
	protected function hideEntry($entry) {
		$entry->hidden_flg = 1;
		$entry->priority_flg = 0;
		$entries = $this->getModel($entry->getStoreName());
		$entries->save($entry);
	}

    /**
     * @param $entry
     */
    public function deleteLogicalEntry($entry) {
        $entries = $this->getModel($entry->getStoreName());
        $entries->deleteLogical($entry);
    }

	/**
	 * @param unknown $entry
	 * @param unknown $priority
	 */
	protected function changePriority($entry, $priority) {
		$entry->priority_flg = $priority;
		$entries = $this->getModel($entry->getStoreName());
		$entries->save($entry);
	}

	/**
	 * @param $entry
	 */
	protected function showEntry($entry) {
		$entry->hidden_flg = 0;
		$entries = $this->getModel($entry->getStoreName());
		$entries->save($entry);
	}

	/**
	 * @param $hide_entry_values
	 * @param null $show_entry
	 * @throws Exception
	 */
	protected function showAndHideEntries($hide_entry_values, $show_entry = null) {

		$this->transaction->begin();

		try {

			if (count($hide_entry_values) > 0) {
				$this->hideEntries($hide_entry_values);
			}

			if ($show_entry) {
				$this->showEntry($show_entry);
			}

		} catch (Exception $e) {
			$this->logger->error("BrandService#showAndHideEntries Error");
			$this->logger->error($e);
			$this->transaction->rollback();
			throw $e;
		}
		$this->transaction->commit();
	}


	/**
	 * @param $panel_name
	 * @param $add_entry_count
	 * @return array
	 */
	protected function getHiddenTargetEntriesByTop($panel_name, $add_entry_count) {

		$hide_entry_values = array();

		$count = $this->redis->lLen($panel_name);
		if ($count > self::TOP_PANEL_MAX_COUNT - $add_entry_count) {
			$hide_entry_values = $this->redis->lRange($panel_name, self::TOP_PANEL_MAX_COUNT - $add_entry_count, $count - 1);
		}
		return $hide_entry_values;
	}


	/**
	 * @param $panel_name
	 * @param $add_entry_count
	 * @return array
	 */
	protected function getHiddenTargetEntriesByNormal($panel_name, $add_entry_count) {

		$hide_entry_values = array();

		$count = $this->redis->lLen($panel_name);
		if ($count > self::NORMAL_PANEL_MAX_COUNT - $add_entry_count) {
			$hide_entry_values = $this->redis->lRange($panel_name, self::NORMAL_PANEL_MAX_COUNT - $add_entry_count, $count - 1);
		}
		return $hide_entry_values;
	}

    /**
     * @return Redis
     * @throws Exception
     * @throws RedisException
     */
    public function createRedis() {

        $host = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.Host');
        $port = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.Port');
        $db_id = aafwApplicationConfig::getInstance()->query('@redis.StoreCache.DbId');

        return aafwRedisManager::getRedisInstance($host, $port, $db_id);
    }

    /**
     * @param $redis
     */
    public function setRedis($redis) {
        $this->redis = $redis;
    }

    /**
     * @return Redis
     */
    public function getRedis() {
        return $this->redis;
    }
}
