<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CommentUserService');

class api_load_comment_list extends BrandcoGETActionBase {

    protected $ContainerName = 'comment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    private $comment_plugin;

    public function validate() {
        $comment_plugin_id = $this->GET['comment_plugin_id'];

        if (Util::isNullOrEmpty($comment_plugin_id)) {
            return true;
        }

        $comment_plugin_validator = new CommentPluginValidator($comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        $this->comment_plugin = $comment_plugin_validator->getCommentPlugin();
        return true;
    }

    function doAction() {
        $data = array();
        $data['comment_plugin_ids'] = array();

        if (Util::isNullOrEmpty($this->comment_plugin)) {
            /** @var CommentPluginService $comment_plugin_service */
            $comment_plugin_service = $this->getService('CommentPluginService');
            $comment_plugins = $comment_plugin_service->getCommentPluginsByBrandId($this->getBrand()->id);

            $data['comment_plugin_ids'] = $this->getCommentPluginIds($comment_plugins);
        } else {
            $data['comment_plugin_ids'][] = $this->comment_plugin->id;
        }

        $data['page'] = $this->GET['page'];
        $data['status'] = $this->GET['status'] ?: CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL;
        $data['discard_flg'] = $this->GET['discard_flg'];
        $data['note_status'] = $this->GET['note_status'];
        $data['sns_share'] = $this->GET['sns_share'];
        $data['order_type'] = $this->GET['order_type'];
        $data['order_kind'] = $this->GET['order_kind'];
        $data['brand_id'] = $this->getBrand()->id;
        $data['page_limit'] = $this->GET['page_limit'] ?: CommentUserService::DISPLAY_20_ITEMS;
        $data['nickname'] = trim($this->GET['nickname']);
        $data['comment_content'] = trim($this->GET['comment_content']);
        $data['bur_no'] = $this->GET['bur_no'];
        $data['from_date'] = $this->GET['from_date'];
        $data['to_date'] = $this->GET['to_date'];

        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $search_conditions = array(
            'status' => $data['status'],
            'bur_no' => $data['bur_no'],
            'nickname' => $data['nickname'],
            'comment_content' => $data['comment_content'],
            'brand_id' => $data['brand_id'],
            'discard_flg' => $data['discard_flg'],
            'note_status' => $data['note_status'],
            'sns_share' => $data['sns_share'],
            'comment_plugin_ids' => $data['comment_plugin_ids'],
            'from_date' => $data['from_date'],
            'to_date'   => $data['to_date']
        );

        $total_count = $comment_user_service->countComment($search_conditions);

        $total_page = floor($total_count / $data['page_limit']) + ($total_count % $data['page_limit'] > 0);
        $data['page'] = Util::getCorrectPaging($data['page'], $total_page);

        $html['pager'] = aafwWidgets::getInstance()->loadWidget('BrandcoDefaultListPager')->render(array(
            'TotalCount' => $total_count,
            'CurrentPage' => $data['page'],
            'Count' => $data['page_limit'],
            'order_kind_labels' => CommentUserRelation::$comment_use_relation_order_kind_label,
            'order_type_labels' => CommentUserRelation::$comment_use_relation_order_type_label,
            'order_type'        => $data['order_type'],
            'order_kind'        => $data['order_kind']
        ));

        $html['comment_list'] = aafwWidgets::getInstance()->loadWidget('CommentList')->render($data);

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * @param $comment_plugins
     * @return array
     */
    public function getCommentPluginIds($comment_plugins) {
        $comment_plugin_ids = array();
        foreach ($comment_plugins as $comment_plugin) {
            $comment_plugin_ids[] = $comment_plugin->id;
        }

        return $comment_plugin_ids;
    }
}
