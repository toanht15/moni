<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CommentPluginService');

class BrandcoItemCountList extends aafwWidgetBase {

    public function doService($params = array()){
        $params['item_count_list'] = array(
            CommentPluginService::DISPLAY_20_ITEMS,
            CommentPluginService::DISPLAY_50_ITEMS,
            CommentPluginService::DISPLAY_100_ITEMS
        );

        return $params;
    }
}