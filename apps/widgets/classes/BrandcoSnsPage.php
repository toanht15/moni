<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');
AAFW::import('jp.aainc.classes.services.SnsPageService');
AAFW::import('jp.aainc.classes.entities.RssEntry');
AAFW::import('jp.aainc.classes.entities.FacebookEntry');
AAFW::import('jp.aainc.classes.entities.LinkEntry');
AAFW::import('jp.aainc.classes.entities.TwitterEntry');
AAFW::import('jp.aainc.classes.entities.YoutubeEntry');
AAFW::import('jp.aainc.classes.entities.UserPanelClick');

class BrandcoSnsPage extends aafwWidgetBase {

    const PAGE = 1;

    public function doService($params = array()) {
        $sns_page_service = new SnsPageService($params['brand_social_account_id']);

        $panel_list = $sns_page_service->getSnsPanelList(self::PAGE);
        $params['panel_list'] = $panel_list;

        $params['sp_panel_per_page'] = PanelServiceBase::DEFAULT_PAGE_COUNT_SP;
        $total_count = $sns_page_service->getTotalCount();
        $params['total_count'] = $total_count;
        $params['load_more_flg'] = count($panel_list) < $total_count;

        return $params;
    }
}
