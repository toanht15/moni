<?php
AAFW::import('jp.aainc.classes.services.StreamService');

class PhotoStreamService extends StreamService {
    const DETAIL_PAGE_LIMIT_PC = 23;
    const DETAIL_PAGE_LIMIT_SP = 11;

    const DEFAULT_PAGE = 1;

    const PANEL_TYPE_HIDDEN = 1;
    const PANEL_TYPE_AVAILABLE = 0;

    public function __construct() {
        parent::__construct('Photo');
    }

    public function getAllStream() {
        return $this->streams->find(array());
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getStreamByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );
        return $this->streams->findOne($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getStreamsByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );
        return $this->streams->find($filter);
    }

    /**
     * トップページに自動で表示するか検閲するかのフラグを更新する
     * @param $stream_id
     * @param $panel_hidden_flg
     * @throws Exception
     */
    public function changeEntryHiddenFlgForStream($stream_id, $panel_hidden_flg) {
        $stream = $this->getStreamById($stream_id);
        $stream->panel_hidden_flg = $panel_hidden_flg;
        $this->streams->save($stream);
    }

    /**
     * トップページに表示するパネル上限数を更新する
     * @param $stream_id
     * @param $display_panel_limit
     */
    public function updateDisplayPanelLimit($stream_id, $display_panel_limit) {
        $stream = $this->getStreamById($stream_id);
        $stream->display_panel_limit = $display_panel_limit;
        $this->streams->save($stream);
    }

    /**
     * トップページに表示可能なエントリー取得
     * @param $stream_id
     * @param $page
     * @param $page_limit
     * @return mixed
     */
    public function getAvailableEntriesByStreamId($stream_id, $page = 0, $page_limit = 0) {
        if (!$stream_id) return array();

        $filter = array(
            'conditions' => array(
                'stream_id' => $stream_id,
                'hidden_flg' => 0
            ),
            'pager' => array(
                'page' => $page ? $page : self::DEFAULT_PAGE,
                'count' => $page_limit ? $page_limit : $this->getPageLimit()
            ),
            'order' => array(
                'name' => 'pub_date',
                'direction' => 'desc'
            )
        );

        return $this->entries->find($filter);
    }

    /**
     * トップページに表示可能なエントリー数取得
     * @param $stream_id
     * @return mixed
     */
    public function getAvailableEntriesCount($stream_id) {
        $filter = array(
            'conditions' => array(
                'stream_id' => $stream_id,
                'hidden_flg' => 0
            )
        );
        return $this->entries->count($filter);
    }

    public function countAllEntry($stream_id) {
        $filter = array(
            'conditions' => array(
                'stream_id' => $stream_id,
            )
        );
        return $this->entries->count($filter);
    }

    public function getPhotoEntryByPhotoUserId($photo_user_id) {
        $filter = array(
            'photo_user_id' => $photo_user_id
        );
        return $this->entries->findOne($filter);
    }

    /**
     * @param bool $is_detail_page
     * @return int
     */
    public function getPageLimit($is_detail_page = false) {
        if ($is_detail_page) {
            return Util::isSmartPhone() ? PhotoStreamService::DETAIL_PAGE_LIMIT_SP : PhotoStreamService::DETAIL_PAGE_LIMIT_PC;
        }

        return Util::isSmartPhone() ? PanelServiceBase::DEFAULT_PAGE_COUNT_SP : PanelServiceBase::DEFAULT_PAGE_COUNT_PC;
    }

    public function filterPanelByLimit($stream, $limit = 0, $order = 'pub_date') {
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
            $i = 1;
            foreach ($display_entries as $entry) {
                if ($i++ <= $limit) continue;
                if (!$normal_panel_service->deleteEntry($brand, $entry)) {
                    $top_panel_service->deleteEntry($brand, $entry);
                }
            }
        }
    }

    public function getPhotoPanelHiddenFlg($stream_panel_hidden_flg, $action_panel_hidden_flg) {
        return $action_panel_hidden_flg || $stream_panel_hidden_flg;
    }

    public function getPhotoEntriesCountByStreamId($stream_id) {
        $filter = array(
            'stream_id' => $stream_id
        );
        return $this->entries->count($filter);
    }

    public function getPhotoEntriesByStreamId($stream_id, $page = 1, $page_limit = 20, $order) {
        $filter = array(
            'conditions' => array(
                'stream_id' => $stream_id
            ),
            'pager' => array(
                'page' => $page,
                'count' => $page_limit
            ),
            'order' => $order
        );

        return $this->entries->find($filter);
    }

    /**
     * BRAND IDから写真アクションID一覧取得
     * @param $brand_id
     * @return mixed
     */
    public function getPhotoActionIdsByBrandId($brand_id) {
        $cp_flow_service = $this->getService('CpFlowService');
        $cps = $cp_flow_service->getPublishedCampaignAndMessage($brand_id);

        foreach ($cps as $cp) {
            $cp_ids[] = $cp->id;
        }
        return $cp_flow_service->getPhotoActionsByCpId($cp_ids);
    }

    public function getEntryIdFromLink($link) {
        $matches = explode(Util::rewriteUrl('photo', 'detail') . '/', $link);
        return $matches[1];
    }
}
