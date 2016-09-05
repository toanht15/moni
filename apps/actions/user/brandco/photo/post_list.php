<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class post_list extends BrandcoGETActionBase {
    public $NeedRedirect = true;
    public $NeedOption = array();

    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $this->createService('PhotoStreamService');

        $stream = $photo_stream_service->getStreamByBrandId($this->getBrand()->id);
        $photo_entries = $photo_stream_service->getAvailableEntriesByStreamId($stream->id);
        if (!$photo_entries) return '403';

        $this->Data['page_data']['page_title'] = 'Photo Gallery';
        $this->Data['page_data']['photo_entries'] = $photo_entries;
        $this->Data['page_data']['sp_panel_per_page'] = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
        $this->Data['page_data']['cp_action_id'] = -1;

        $total_count = $photo_stream_service->getAvailableEntriesCount($stream->id);
        $this->Data['page_data']['total_count'] = $total_count;
        $this->Data['page_data']['load_more_flg'] = $photo_entries->total() < $total_count;
        $this->Data['pageStatus']['og']['url'] = Util::rewriteUrl('photo', 'post_list');

        $this->Data['brand_contract'] = BrandInfoContainer::getInstance()->getBrandContract();

        return 'user/brandco/photo/post_list.php';
    }
}