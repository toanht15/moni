<?php
AAFW::import('jp.aainc.classes.services.StreamService');

class CpInstagramHashtagStreamService extends StreamService {

    const DETAIL_PAGE_LIMIT_PC = 22;
    const DETAIL_PAGE_LIMIT_SP = 10;

    const DEFAULT_PAGE = 1;

    const PANEL_TYPE_HIDDEN = 1;
    const PANEL_TYPE_AVAILABLE = 0;

    public function __construct() {
        parent::__construct('CpInstagramHashtag');
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
     * 表示可能なエントリー取得
     * @param $stream_id
     * @param $page
     * @param $page_limit
     * @return mixed
     */
    public function getAvailableEntriesByStreamId($stream_id, $page, $page_limit) {
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
     * 表示可能なエントリー数取得
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

    public function getCpInstagramHashtagEntryByInstagramHashtagUserPostIdAndStreamId($instagram_hashtag_user_post_id, $stream_id) {
        $filter = array(
            'instagram_hashtag_user_post_id' => $instagram_hashtag_user_post_id,
            'stream_id' => $stream_id
        );
        return $this->entries->findOne($filter);
    }

    public function getCpInstagramHashtagPanelHiddenFlg($action_approval_flg, $stream_panel_hidden_flg) {
        return $action_approval_flg || $stream_panel_hidden_flg;
    }
}
