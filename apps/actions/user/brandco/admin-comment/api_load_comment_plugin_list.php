<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CommentPluginService');

class api_load_comment_plugin_list extends BrandcoGETActionBase {

    protected $ContainerName = 'plugin_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');

        $data = array();
        $data['page'] = $this->GET['page'];
        $data['brand_id'] = $this->getBrand()->id;
        $data['order_type'] = $this->GET['order_type'] ?: CommentPlugin::ORDER_TYPE_DESC;
        $data['page_limit'] = $this->GET['page_limit'] ?: CommentPluginService::DISPLAY_20_ITEMS;
        $data['type'] = $this->GET['type'] ?: CommentPlugin::COMMENT_PLUGIN_TYPE_ALL;

        $conditions = array(
            'type' => $data['type'],
            'brand_id' => $data['brand_id']
        );

        $total_count = $comment_plugin_service->countCommentPluginsByBrandId($conditions);

        $total_page = floor($total_count / $data['page_limit']) + ($total_count % $data['page_limit'] > 0);
        $data['page'] = Util::getCorrectPaging($data['page'], $total_page);

        $html['pager'] = aafwWidgets::getInstance()->loadWidget('BrandcoDefaultListPager')->render(array(
            'TotalCount' => $total_count,
            'CurrentPage' => $data['page'],
            'Count' => $data['page_limit'],
        ));

        $html['plugin_list'] = aafwWidgets::getInstance()->loadWidget('CommentPluginList')->render($data);

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
