<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');
AAFW::import('jp.aainc.classes.services.TopPageService');
AAFW::import('jp.aainc.classes.entities.RssEntry');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.YoutubeEntry');
AAFW::import('jp.aainc.classes.entities.UserPanelClick');
AAFW::import('jp.aainc.classes.entities.PhotoEntry');

class BrandcoTopMainCol extends aafwWidgetBase {

    const PANEL_TYPE_TOP = 'Top';
    const PANEL_TYPE_NORMAL = 'Normal';
    const PAGE = 1;

    public function doService($params = array()) {

        if (Util::isSmartPhone()) {
            $media_type = TopPageService::MEDIA_TYPE_SP;
            $count = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
        } else {
            $media_type = TopPageService::MEDIA_TYPE_PC;
            $count = PanelServiceBase::DEFAULT_PAGE_COUNT_PC;
        }

        if ($count > PanelServiceBase::TOP_PANEL_MAX_COUNT) {
            $topPanelCount = PanelServiceBase::TOP_PANEL_MAX_COUNT;
        } else {
            $topPanelCount = $count;
        }

        $top_page_service = new TopPageService();
        $panel_list = $top_page_service->getAllPanelList($params['brand'], $media_type, self::PAGE, $topPanelCount, $count);
        $params['panel_list'] = $panel_list;
        $total_count = $top_page_service->getTotalCount($params['brand']);
        $params['total_count'] = $total_count;
        $params['sp_page_per_count'] = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
        return $params;
    }
}
