<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class page_entries extends BrandcoGETActionBase {
    const PAGE_LIMIT = 20;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $page_stream_service;

    public function validate() {
        $this->page_stream_service = $this->createService('PageStreamService');
        $this->Data['stream'] = $this->page_stream_service->getStreamByBrandId($this->getBrand()->id);

        return !$this->Data['stream']->id ? false : true;
    }

    public function doAction() {
        $static_html_entry_service = $this->createService('StaticHtmlEntryService');
        $static_html_entry_ids = $static_html_entry_service->getPublicEntryIdByBrandId($this->getBrand()->id);

        $this->Data['page_limited'] = self::PAGE_LIMIT;
        $this->Data['total_entries_count'] = $this->page_stream_service->getEntriesCountByStaticHtmlEntryIds($static_html_entry_ids);

        $total_page = floor($this->Data['total_entries_count'] / self::PAGE_LIMIT) + ($this->Data['total_entries_count'] % self::PAGE_LIMIT > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $order = array(
            'name' => "pub_date",
            'direction' => "desc",
        );

        $this->Data['page_entries'] = $this->page_stream_service->getEntriesByStaticHtmlEntryIds($static_html_entry_ids, $this->p, self::PAGE_LIMIT, $order);

        return 'user/brandco/admin-top/page_entries.php';
    }
}