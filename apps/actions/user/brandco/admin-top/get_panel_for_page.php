<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');
AAFW::import('jp.aainc.classes.services.TopPageService');
AAFW::import('jp.aainc.widgets.classes.BrandcoTopMainCol');
AAFW::import('jp.aainc.classes.entities.RssEntry');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.YoutubeEntry');


class get_panel_for_page extends BrandcoGETActionBase {

    protected $pageCount;
    public $NeedOption = array();

    public function validate() {
        if ($this->isEmpty($this->p) || $this->p < 1 || !$this->isNumeric($this->p)) return '403';

        return true;
    }

    function doAction() {

        $brand = $this->getBrand();
        $top_panel_service = $this->createService('TopPanelService');

        if (Util::isSmartPhone()) {
            $this->pageCount = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
            $media_type = TopPageService::MEDIA_TYPE_SP;
        } else {
            $this->pageCount = PanelServiceBase::DEFAULT_PAGE_COUNT_PC;
            $media_type = TopPageService::MEDIA_TYPE_PC;
        }

        $top_count = $top_panel_service->count($brand);
        $offset = ($this->p - 1) * $this->pageCount - $top_count;
        $limit = $offset + $this->pageCount - 1;

        $top_page_service = new TopPageService();
        $panel_list = $top_page_service->getNormalPanelList($brand, $media_type, $this->p, $offset, $limit);

        $this->Data['isLoginAdmin'] = ($this->preview) ? false : ( Util::isSmartPhone() ? false : $this->isLoginAdmin() );
        $this->Data['brand'] = $brand;
        $this->Data['panel_list'] = $panel_list;

        return 'user/brandco/admin-top/get_panel_for_page.php';
    }
}