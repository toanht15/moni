<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.services.SnsPageService');
AAFW::import('jp.aainc.widgets.classes.BrandcoTopMainCol');
AAFW::import('jp.aainc.classes.entities.RssEntry');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.YoutubeEntry');

class get_panel_for_page extends BrandcoGETActionBase {

    public $NeedOption = array();

    protected $pageCount;

    public function validate() {
        if ($this->isEmpty($this->p) || $this->p < 1 || !$this->isNumeric($this->p)) return '403';

        return true;
    }

    function doAction() {
        $sns_page_service = new SnsPageService($this->b);

        $panel_list = $sns_page_service->getSnsPanelList($this->p);
        $this->Data['panel_list'] = $panel_list;

        return 'user/brandco/sns/get_panel_for_page.php';
    }
}