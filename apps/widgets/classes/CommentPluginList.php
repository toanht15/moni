<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class CommentPluginList extends aafwWidgetBase {

    private $static_html_entry_service;

    public function doService($params = array()){
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');
        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $conditions = array(
            'type' => $params['type'],
            'brand_id' => $params['brand_id']
        );

        $pager = array(
            'page' => $params['page'],
            'count' => $params['page_limit']
        );

        $order = array(
            'name' => 'updated_at',
            'direction' => CommentPlugin::$order_types[$params['order_type']]
        );

        $params['comment_plugin_list'] = array();
        $comment_plugin_list = $comment_plugin_service->getCommentPluginList($conditions, $pager, $order);

        foreach ($comment_plugin_list as $comment_plugin) {
            $comment_plugin->comment_count = $comment_plugin_service->getCommentCountByCommentPluginId($comment_plugin->id, $params['brand_id']);
            $comment_plugin->share_info = $comment_plugin_service->getCommentPluginShareInfo($comment_plugin->id, $params['brand_id']);
            $comment_plugin->share_sns_list = $comment_plugin_service->getCommentPluginShareSnsList($comment_plugin->id);
            $comment_plugin->last_comment_user = $comment_user_service->getLastCommentUser($comment_plugin->id);
            $comment_plugin->edit_url = $this->getEditUrl($comment_plugin);

            $params['comment_plugin_list'][] = $comment_plugin;
        }

        return $params;
    }

    /**
     * @return モデル
     */
    public function getStaticHtmlEntryService() {
        if (!$this->static_html_entry_service) {
            /** @var StaticHtmlEntryService $static_html_entry_service */
            $this->static_html_entry_service = $this->getService('StaticHtmlEntryService');
        }

        return $this->static_html_entry_service;
    }

    /**
     * @param $comment_plugin
     * @return string
     */
    public function getEditUrl($comment_plugin) {
        if ($comment_plugin->type == CommentPlugin::COMMENT_PLUGIN_TYPE_EXTERNAL) {
            return Util::rewriteUrl('admin-comment', 'comment_plugin', array($comment_plugin->id));
        }

        $static_html_entry_service = $this->getStaticHtmlEntryService();
        $static_html_entry = $static_html_entry_service->getEntryById($comment_plugin->static_html_entry_id);

        if (Util::isNullOrEmpty($comment_plugin->static_html_entry_id) || Util::isNullOrEmpty($static_html_entry)) {
            return '';
        }

        if ($static_html_entry->isEmbedPage()) {
            return Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page_form', array($static_html_entry->id));
        }

        return Util::rewriteUrl('admin-blog', 'edit_static_html_entry_form', array($static_html_entry->id));
    }
}