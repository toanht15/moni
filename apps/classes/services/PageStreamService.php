<?php
AAFW::import('jp.aainc.classes.services.StreamService');

class PageStreamService extends StreamService {

    public function __construct() {
        parent::__construct('Page');
    }

    /**
     * @return mixed
     */
    public function getAllStream() {
        return $this->streams->find(array());
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getStreamByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            )
        );

        return $this->streams->findOne($filter);
    }

    /**
     * @param $static_html_entry_id
     * @return mixed
     */
    public function getEntryByStaticHtmlEntryId($static_html_entry_id) {
        $filter = array(
            'static_html_entry_id' => $static_html_entry_id
        );

        return $this->entries->findOne($filter);
    }

    /**
     * @param $stream_id
     * @param $params
     * @return mixed
     */
    public function getAvailableEntryByStreamId($stream_id, $params) {
        $date = new DateTime();

        $filter = array(
            'stream_id'         => $stream_id,
            'hidden_flg'        => 1,
            'manual_off_flg'    => 0,
            'pub_date:<'        => $date->format('Y-m-d H:i')
        );

        if (isset($params['top_hidden_flg'])) {
            $filter['top_hidden_flg'] = $params['top_hidden_flg'];
        }

        return $this->entries->find($filter);
    }

    /**
     * @param $stream_id
     * @return mixed
     */
    public function getHiddenEntryByStreamId($stream_id) {
        $date = new DateTime();

        $filter = array(
            'stream_id' => $stream_id,
            'hidden_flg'        => 0,
            'pub_date:>'        => $date->format('Y-m-d H:i')
        );

        return $this->entries->find($filter);
    }

    public function getEntriesCountByStaticHtmlEntryIds($static_html_entry_ids) {
        $filter = array(
            'static_html_entry_id' => $static_html_entry_ids
        );

        return $this->entries->count($filter);
    }

    public function getEntriesByStaticHtmlEntryIds($static_html_entry_ids, $page, $page_limit, $order) {
        $filter = array(
            'conditions' => array(
                'static_html_entry_id' => $static_html_entry_ids
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
     * @param $static_html_entry_ids
     * @return mixed
     */
    public function getAllHiddenEntriesByStaticEntryIds($static_html_entry_ids) {
        $filter = array(
            'static_html_entry_id' => $static_html_entry_ids,
            'top_hidden_flg' => 1
        );

        return $this->entries->find($filter);
    }

    /**
     * @param $entry
     * @param $static_html_entry
     * @return mixed
     */
    public function staticHtmlToPageEntry($entry, $static_html_entry) {
        $entry->static_html_entry_id = $static_html_entry->id;
        $entry->pub_date = $static_html_entry->public_date;

        if (!$entry->image_url) {
            $entry->image_url = $static_html_entry->og_image_url;
        }

        if (!$entry->panel_text) {
            $entry->panel_text = $static_html_entry->getBriefBody();
        }

        return $entry;
    }

    /**
     * @param $entry
     */
    public function deleteEntry($entry) {
        $this->entries->delete($entry);
    }

    /**
     * @param $stream_id
     * @param $panel_hidden_flg
     * @throws Exception
     */
    public function changeEntryHiddenFlgForStream($stream_id, $panel_hidden_flg) {
        $stream = $this->getStreamById($stream_id);
        $stream->panel_hidden_flg = $panel_hidden_flg;
        $this->updateStream($stream);
    }

    /**
     * @param $stream_id
     * @param $display_panel_limit
     * @throws Exception
     */
    public function updateDisplayPanelLimit($stream_id, $display_panel_limit) {
        $stream = $this->getStreamById($stream_id);
        $stream->display_panel_limit = $display_panel_limit;
        $this->streams->save($stream);
    }

    /**
     * @param $stream
     * @param int $limit
     * @param string $order
     * @return bool
     * @throws Exception
     */
    public function filterPanelByLimit($stream, $limit = 0, $order = 'pub_date') {
        if (!$limit) return false;

        $date = new DateTime();
        $filter = array(
            'conditions' => array(
                'hidden_flg' => 0,
                'stream_id' => $stream->id,
                'pub_date:<' => $date->format('Y-m-d H:i')
            ),
            'order' => array(
                'name' => $order,
                'direction' => "desc"
            )
        );

        $display_entries = $this->entries->find($filter);

        if ($display_entries && $display_entries->total() > $limit) {
            /** @var NormalPanelService $normal_panel_service */
            $normal_panel_service = $this->service_factory->create('NormalPanelService');
            /** @var TopPanelService $top_panel_service */
            $top_panel_service = $this->service_factory->create('TopPanelService');
            /** @var BrandService $brand_service */
            $brand_service = $this->service_factory->create('BrandService');
            /** @var PageStreamService $page_stream_service */
            $page_stream_service = $this->service_factory->create('PageStreamService');

            $brand = $brand_service->getBrandById($stream->brand_id);
            $i = 1;

            foreach ($display_entries as $entry) {
                if ($entry->getStaticHtmlEntry()->hidden_flg) continue;

                if ($i++ <= $limit) continue;

                $entry->top_hidden_flg = 1;
                $entry->priority_flg = 0;

                if (!$normal_panel_service->deleteEntry($brand, $entry)) {
                    $top_panel_service->deleteEntry($brand, $entry);
                } else {
                    $page_stream_service->updateEntry($entry);
                }
            }

            return true;
        }

        return false;
    }
}