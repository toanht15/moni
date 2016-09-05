<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');

class StreamService extends aafwServiceBase {

	const STREAM_TYPE_TWITTER = "twitter";
	const STREAM_TYPE_FACEBOOK = "facebook";
	const STREAM_TYPE_YOUTUBE = "youtube";
    const STREAM_TYPE_RSS = "rss";
    const STREAM_TYPE_LINK = "link";
    const STREAM_TYPE_INSTAGRAM = "instagram";
    const STREAM_TYPE_PHOTO = 'photo';
    const STREAM_TYPE_PAGE = 'page';
    const STREAM_TYPE_CP_INSTAGRAM_HASHTAG = 'cp_instagram_hashtag';

    const POST_TYPE_PANEL = 'panel';

    public $sns_domain_list = array(
        self::STREAM_TYPE_TWITTER   => 'twitter.com',
        self::STREAM_TYPE_FACEBOOK  => 'www.facebook.com',
        self::STREAM_TYPE_YOUTUBE   => 'www.youtube.com'
    );

    protected $stream_type;
	protected $streams;
	protected $entries;
	protected $service_factory;
	protected $crawler_service;
    protected $brand_social_account_service;
	protected $logger;

	public function __construct($stream_type) {
		$this->stream_type = $stream_type;
		$this->streams = $this->getModel($this->stream_type . "Streams");
		$this->entries = $this->getModel($this->stream_type . "Entries");
		$this->service_factory = new aafwServiceFactory ();
		$this->crawler_service = $this->service_factory->create('CrawlerService');
        $this->brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	#-----------------------------------------------------------
	# Stream
	#-----------------------------------------------------------

	public function getStreamById($stream_id) {
		$conditions = array(
			"id" => $stream_id,
		);
		return $this->streams->findOne($conditions);
	}

	public function getStreamsByIds($stream_ids) {
		$conditions = array(
			"id" => $stream_ids,
		);
		return $this->streams->find($conditions);
	}

	public function getAvailableStreamsByStreamIds($stream_ids) {
		$conditions = array(
			"id" => $stream_ids,
			"hidden_flg" => 0
		);
		return $this->streams->find($conditions);
	}

    public function getAvailableStreams() {
        $conditions = array(
            "hidden_flg" => 0,
        );

        return $this->streams->find($conditions);
    }

	public function getAvailableStreamsByBrandId($brand_id) {
		$conditions = array(
			"brand_id" => $brand_id,
			"hidden_flg" => 0,
		);

		return $this->streams->find($conditions);
	}

	public function getStreamsByBrandId($brand_id, $flg_get_1 = false) {
		$conditions = array(
			"brand_id" => $brand_id
		);

		if ($flg_get_1) {
			return $this->streams->findOne($conditions);
		}

		return $this->streams->find($conditions);
	}

	public function createEmptyStream() {
		return $this->streams->createEmptyObject();
	}

	public function createStream($stream) {
		$this->streams->save($stream);
	}

	public function updateStream($stream) {
		$this->streams->save($stream);
	}

	public function deleteStream($stream) {
		$this->streams->delete($stream);
	}

	#-----------------------------------------------------------
	# Entry for API (hidden_flg == 0)
	# あえてメソッド名を分ける
	#-----------------------------------------------------------
	public function getAvailableEntriesCountByStreamId($stream_ids) {

		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_ids,
				"hidden_flg" => 0,
			),
		);
		return $this->entries->count($filter);
	}

	/**
	 * 表示状態のエントリーを取得する。
	 * @param $stream_id
	 * @param $hidden_flg
	 * @return mixed
	 */
	public function getAllEntriesByStreamIdAndHiddenFlg($stream_id, $hidden_flg) {
		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
				"hidden_flg" => $hidden_flg,
			),
		);
		return $this->entries->find($filter);
	}


	public function getAvailableEntriesByStreamId($stream_id, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
				"hidden_flg" => 0,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

	public function getEntriesByEntryIdsForAPI($entry_ids, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_ids,
				"hidden_flg" => 0,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

	public function getEntryByEntryIdForAPI($entry_id) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_id,
				"hidden_flg" => 0,
			),
		);
		return $this->entries->findOne($filter);
	}

	public function getEntryForAPI($stream_id, $entry_id) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_id,
				"stream_id" => $stream_id,
				"hidden_flg" => 0,
			),
		);
		return $this->entries->findOne($filter);
	}

	public function getAvailableEntriesByEntryIds($entry_ids) {
		$conditions = array(
			"id" => $entry_ids,
			"hidden_flg" => 0,
		);
		return $this->entries->find($conditions);
	}

	public function getEntriesCountByEntryIdsForAPI($entry_ids) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_ids,
				"hidden_flg" => 0,
			),
		);
		return $this->entries->count($filter);
	}

	public function getEntriesByCreatorIdForAPI($creator_id, $page, $count, $order, $creator_flg = true) {

		$filter = array(
			'conditions' => array(
				"hidden_flg" => 0,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);

		if ($creator_flg) {
			$filter['conditions']['creator_id'] = $creator_id;
		} else {
			$filter['conditions']['creator_id:!='] = $creator_id;
		}

		return $this->entries->find($filter);
	}

	public function getEntriesCountByCreatorIdForAPI($creator_id, $creator_flg = true) {

		$filter = array(
			'conditions' => array(
				"hidden_flg" => 0,
			),
		);

		if ($creator_flg) {
			$filter['conditions']['creator_id'] = $creator_id;
		} else {
			$filter['conditions']['creator_id:!='] = $creator_id;
		}
		return $this->entries->count($filter);
	}

	#-----------------------------------------------------------
	# Entry for Admin
	#-----------------------------------------------------------
	public function getEntriesCountByStreamIds($stream_id) {
		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
			),
		);
		return $this->entries->count($filter);
	}

	public function getEntriesByStreamId($stream_id, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_id,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

	public function getEntriesByStreamIds($stream_ids, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_ids,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

	public function getHiddenEntriesByStreamIds($stream_ids, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"stream_id" => $stream_ids,
				"hidden_flg" => 1,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

    public function getAllEntriesByStreamId ($stream_id) {
        $filter = array(
            'conditions' => array(
                "stream_id" => $stream_id
            )
        );
        return $this->entries->find($filter);
    }

    public function getAllEntriesCountByStreamId($stream_id) {
        $filter = array(
            'conditions' => array(
                "stream_id" => $stream_id
            )
        );
        return $this->entries->count($filter);
    }

	public function getEntriesByEntryIds($entry_ids, $page, $count, $order) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_ids,
			),
			'pager' => array(
				'page' => $page,
				'count' => $count,
			),
			'order' => $order,
		);
		return $this->entries->find($filter);
	}

	public function getEntryByEntryId($entry_id) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_id,
			),
		);
		return $this->entries->findOne($filter);
	}

	public function createEmptyEntry() {
		return $this->entries->createEmptyObject();
	}


	public function getStreamType() {
		switch ($this->stream_type) {
			case 'Twitter':
				$stream_type = self::STREAM_TYPE_TWITTER;
				break;
			case 'Facebook':
				$stream_type = self::STREAM_TYPE_FACEBOOK;
				break;
            case 'Youtube':
                $stream_type = self::STREAM_TYPE_YOUTUBE;
                break;
            case 'Rss':
                $stream_type = self::STREAM_TYPE_RSS;
                break;
            case 'Instagram':
                $stream_type = self::STREAM_TYPE_INSTAGRAM;
                break;
            case 'CpInstagramHashtag':
                $stream_type = self::STREAM_TYPE_CP_INSTAGRAM_HASHTAG;
                break;
			default:
				$stream_type = "none";
				break;
		}

		return $stream_type;
	}

	public function createEntry($entry) {
		$this->entries->save($entry);
	}

	public function updateEntry($entry) {
		$this->entries->save($entry);
	}

	/**
	 * @param $entry
	 * @throws Exception
	 */
	public function changeDisplayType($entry) {

		try {
            switch ($entry->display_type) {
                case PanelServiceBase::ENTRY_DISPLAY_TYPE_SMALL:
                    $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_MIDDLE;
                    break;
                case PanelServiceBase::ENTRY_DISPLAY_TYPE_MIDDLE:
                    $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_LARGE;
                    break;
                case PanelServiceBase::ENTRY_DISPLAY_TYPE_LARGE:
                    $entry->display_type = PanelServiceBase::ENTRY_DISPLAY_TYPE_SMALL;
                    break;
                default:
                    break;
            }
			$this->updateEntry($entry);
		} catch (Exception $e) {
			$this->logger->error("StreamService#updateDetail Error");
			$this->logger->error($e);
			$this->streams->rollback();
			throw $e;
		}
	}

	public function getEntryById($entryId) {
		$conditions = array(
			"id" => $entryId,
		);
		return $this->entries->findOne($conditions);
	}

	public function getAllEntry() {
		return $this->entries->findAll();
	}

	/**
	 * @param $brand_id
	 * @param $stream_id
	 * @return mixed
	 */
	public function checkStreamOwner($brand_id, $stream_id) {

		$filter = array(
			'conditions' => array(
				"id" => $stream_id,
				"brand_id" => $brand_id,
				"hidden_flg" => 0,
			),
		);
		$count = $this->streams->count($filter);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param $brand_id
	 * @param $entry_id
	 * @return bool
	 */
	public function checkEntryOwnerByEntryId($brand_id, $entry_id) {

		$filter = array(
			'conditions' => array(
				"id" => $entry_id,
				"brand_id" => $brand_id,
			),
		);
		$entry = $this->entries->findOne($filter);
		if ($entry) {
			$stream = $this->getStreamById($entry->stream_id);
			if ($stream->hidden_flg == "0") {
				return true;
			}
		}
		return false;
	}


	public function countStreamByBrand($brand) {
		$filter = array(
			'conditions' => array(
				'brand_id' => $brand->id,
				'hidden_flg' => 0,
			),
		);

		$count = $this->streams->count($filter);

		return $count;
	}

    public function getAvailableEntryById($entry_id) {
        $conditions = array(
            'id' => $entry_id,
            'hidden_flg' => 0
        );

        return $this->entries->findOne($conditions);
    }

	public function getPreviousEntryId($entry_id, $stream_id) {

		$conditions = array(
			"id:<" => $entry_id,
			"stream_id" => $stream_id,
			"hidden_flg" => 0,
		);
		return $this->entries->getMax("id", $conditions);
	}

	public function getNextEntryId($entry_id, $stream_id) {

		$conditions = array(
			"id:>" => $entry_id,
			"stream_id" => $stream_id,
			"hidden_flg" => 0,
		);
		return $this->entries->getMin("id", $conditions);
	}

	/**
	 * @param $brand_id
	 * @param $page
	 * @param $count
	 * @param $order
	 * @return mixed
	 */
	public function getAllHiddenEntries($brand_id, $page, $count, $order) {

		$streams = $this->getStreamsByBrandId($brand_id);
		$stream_ids = array();
		foreach ($streams as $stream) {
            if ($stream->hidden_flg == 0) {
                $stream_ids[] = $stream->id;
            }
		}
        if (count($stream_ids) > 0) {
            return $this->getHiddenEntriesByStreamIds($stream_ids, $page, $count, $order);
        } else {
            return null;
        }
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
     * @param $link
     * @return mixed
     */
    public function getEntryByLinkUrl($link_url) {
        $filter = array(
            "link" => $link_url
        );
        return $this->entries->findOne($filter);
    }

    /**
     * @param $stream_id
     */
    public function hideAllEntries($stream_id) {
        $filter = array(
            'conditions' => array(
                'stream_id' => $stream_id,
                'hidden_flg' => 0
            )
        );
        $entries = $this->entries->find($filter);
        try {
            foreach ($entries as $entry) {
                $entry->hidden_flg = 1;
                $entry->priority_flg = 0;
                $this->entries->save($entry);
            }
        } catch (Exception $e) {
            $this->logger->error('hideAllEntries Error.' . $e);
        }
    }

    /**
     * @param $stream
     * @param null $limit
     * @param string $order
     */
    public function filterPanelByLimit($stream, $limit = null, $order = 'updated_at') {
        if (!$limit) return;
        $filter = array(
            'conditions' => array(
                'hidden_flg' => 0,
                'stream_id' => $stream->id
            ),
            'order' => array(
                'name' => $order,
                'direction' => "desc"
            )
        );
        $display_entries = $this->entries->find($filter);

        if (!$display_entries) return;

        if ($display_entries->total() > $limit) {
            /** @var NormalPanelService $normal_panel_service */
            $normal_panel_service = $this->service_factory->create('NormalPanelService');
            /** @var TopPanelService $top_panel_service */
            $top_panel_service = $this->service_factory->create('TopPanelService');
            /** @var BrandService $brand_service */
            $brand_service = $this->service_factory->create('BrandService');
            $brand = $brand_service->getBrandById($stream->brand_id);
            $i=1;
            foreach($display_entries as $entry) {
                if($i++ <= $limit) continue;
                if (!$normal_panel_service->deleteEntry($brand, $entry)){
                    $top_panel_service->deleteEntry($brand, $entry);
                }
            }
        }
    }

    public function deletePhysicalStreamAndEntries($stream) {
        $entries = $this->getAllEntriesByStreamId($stream->id);
        try {
            foreach ($entries as $entry) {
                $this->entries->deletePhysical($entry);
            }

            $this->streams->deletePhysical($stream);
        } catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param string $protocol
     * @return string
     */
    public function getSnsUrl($protocol = 'https') {
        $stream_type = strtolower($this->stream_type);
        return $protocol . '://' . $this->sns_domain_list[$stream_type] . '/';
    }
}
