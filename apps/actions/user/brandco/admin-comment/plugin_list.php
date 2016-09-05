<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CommentPluginService');

class plugin_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        $this->Data['type'] = CommentPlugin::COMMENT_PLUGIN_TYPE_ALL;
        $this->Data['order_type'] = CommentPlugin::ORDER_TYPE_DESC;
        $this->Data['page_limit'] = CommentPluginService::DISPLAY_20_ITEMS;

        return 'user/brandco/admin-comment/plugin_list.php';
    }
}