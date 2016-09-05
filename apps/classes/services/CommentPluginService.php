<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CommentPluginService extends aafwServiceBase {

    const DISPLAY_20_ITEMS  = 20;
    const DISPLAY_50_ITEMS  = 50;
    const DISPLAY_100_ITEMS = 100;

    private $data_builder;

    private $comment_plugins;
    private $comment_plugin_actions;
    private $comment_free_text_actions;
    private $comment_plugin_share_settings;

    public function __construct() {
        $this->comment_plugins = $this->getModel('CommentPlugins');
        $this->comment_plugin_actions = $this->getModel('CommentPluginActions');
        $this->comment_free_text_actions = $this->getModel('CommentFreeTextActions');
        $this->comment_plugin_share_settings = $this->getModel('CommentPluginShareSettings');

        $this->data_builder = aafwDataBuilder::newBuilder();
    }

    // CommentPlugins

    /**
     * @return mixed
     */
    public function createEmptyCommentPlugin() {
        return $this->comment_plugins->createEmptyObject();
    }

    /**
     * @param $brand_id
     * @param $static_html_entry_id
     * @return mixed
     */
    public function getCommentPlugin($brand_id, $static_html_entry_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'type' => CommentPlugin::COMMENT_PLUGIN_TYPE_INTERNAL,
            'static_html_entry_id' => $static_html_entry_id
        );

        return $this->comment_plugins->findOne($filter);
    }

    /**
     * @param $plugin_code
     * @return mixed
     */
    public function getCommentPluginByCode($plugin_code) {
        $filter = array(
            'plugin_code' => $plugin_code
        );

        return $this->comment_plugins->findOne($filter);
    }

    /**
     * @param $brand_id
     * @param $static_html_entry_id
     * @return mixed
     */
    public function getActiveCommentPlugin($brand_id, $static_html_entry_id) {
        $filter = array(
            'type' => CommentPlugin::COMMENT_PLUGIN_TYPE_INTERNAL,
            'status' => CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC,
            'brand_id' => $brand_id,
            'static_html_entry_id' => $static_html_entry_id
        );

        return $this->comment_plugins->findOne($filter);
    }

    /**
     * @param $comment_plugin_id
     * @return mixed
     */
    public function getCommentPluginById($comment_plugin_id) {
        return $this->comment_plugins->findOne($comment_plugin_id);
    }

    /**
     * @param $conditions
     * @param null $pager
     * @param null $order
     * @return mixed
     * @throws Exception
     */
    public function getCommentPluginList($conditions, $pager = null, $order = null) {
        $search_conditions = $this->filterSearchCondition($conditions);

        if (Util::isNullOrEmpty($order)) {
            $order = array(
                'name' => 'updated_at',
                'direction' => 'desc'
            );
        }

        if (Util::isNullOrEmpty($pager)) {
            $pager = array(
                'page' => 1,
                'count' => self::DISPLAY_20_ITEMS
            );
        }

        $filter = array(
            'conditions' => $search_conditions,
            'pager' => $pager,
            'order' => $order
        );

        return $this->comment_plugins->find($filter);
    }

    /**
     * @param $comment_plugin_id
     * @param $brand_id
     * @return int
     */
    public function getCommentCountByCommentPluginId($comment_plugin_id, $brand_id) {
        if (!$comment_plugin_id) return 0;
        $conditions = array(
            'brand_id' => $brand_id,
            'comment_plugin_id' => $comment_plugin_id
        );

        $result = $this->data_builder->countCommentByCommentPluginId($conditions);
        return $result[0]['comment_count'];
    }

    /**
     * @param $comment_plugin_id
     * @param $brand_id
     * @return array
     */
    public function getCommentPluginShareInfo($comment_plugin_id, $brand_id) {
        $condition = array(
            'comment_plugin_id' => $comment_plugin_id
        );

        $share_info = array();
        $result = $this->data_builder->getCommentPluginShareInfo($condition);
        foreach ($result as $data) {
            $share_info[$data['social_media_id']]['share_count'] = $data['share_count'];

            if ($data['share_count'] <= 0) {
                continue;
            }

            $friend_count_rs = $this->data_builder->getCommentPluginFriendCount(array(
                'brand_id' => $brand_id,
                'comment_plugin_id' => $comment_plugin_id,
                'social_media_id' => $data['social_media_id']
            ));
            $share_info[$data['social_media_id']]['friend_count'] = $friend_count_rs[0]['friend_count'];
        }

        return $share_info;
    }

    /**
     * @param $brand_id
     * @return array
     */
    public function getCommentPluginsByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->comment_plugins->find($filter);
    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function countCommentPluginsByBrandId($conditions) {
        $search_conditions = $this->filterSearchCondition($conditions);

        return $this->comment_plugins->count($search_conditions);
    }

    /**
     * @param $comment_plugin
     */
    public function updateCommentPlugin($comment_plugin) {
        $this->comment_plugins->save($comment_plugin);
    }

    /**
     * @param $search_condition
     */
    public function filterSearchCondition($search_condition) {
        if ($search_condition['type'] == CommentPlugin::COMMENT_PLUGIN_TYPE_ALL) {
            unset($search_condition['type']);
        }

        return $search_condition;
    }

    /**
     * @param $comment_plugin
     * @return string
     */
    public function getShareUrl($comment_plugin, $request_url) {
        if (!Util::isNullOrEmpty($comment_plugin->share_url)) {
            return $comment_plugin->share_url;
        }

        if (!Util::isNullOrEmpty($request_url)) {
            return Util::stripQueryString($request_url);
        }

        return $this->getStaticHtmlEntryUrl($comment_plugin->static_html_entry_id);
    }

    /**
     * @param $static_html_entry_id
     * @return string
     */
    public function getStaticHtmlEntryUrl($static_html_entry_id) {
        if (Util::isNullOrEmpty($static_html_entry_id) || $static_html_entry_id == 0) {
            return '';
        }

        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->getService('StaticHtmlEntryService');
        $static_html_entry = $static_html_entry_service->getEntryById($static_html_entry_id);

        if (Util::isNullOrEmpty($static_html_entry)) {
            return '';
        }

        return Util::rewriteUrl('', 'page', array($static_html_entry->page_url));
    }

    // CommentPluginShareSettings

    /**
     * @param $comment_plugin_id
     * @return mixed
     */
    public function getCommentPluginShareSettings($comment_plugin_id) {
        $filter = array(
            'comment_plugin_id' => $comment_plugin_id
        );

        return $this->comment_plugin_share_settings->find($filter);
    }

    /**
     * @param $comment_plugin_id
     * @return array
     */
    public function getCommentPluginShareSnsList($comment_plugin_id) {
        $comment_plugin_share_sns_list = array();
        $comment_plugin_share_settings = $this->getCommentPluginShareSettings($comment_plugin_id);

        foreach ($comment_plugin_share_settings as $comment_plugin_share_setting) {
            $comment_plugin_share_sns_list[] = $comment_plugin_share_setting->social_media_id;
        }

        return $comment_plugin_share_sns_list;
    }

    /**
     * @param $comment_plugin_id
     * @param $sns_list
     */
    public function updateCommentPluginShareSettings($comment_plugin_id, $sns_list) {
        $this->deleteCommentPluginShareSettings($comment_plugin_id);

        foreach ($sns_list as $sns_id) {
            $comment_plugin_share_setting = $this->comment_plugin_share_settings->createEmptyObject();

            $comment_plugin_share_setting->comment_plugin_id = $comment_plugin_id;
            $comment_plugin_share_setting->social_media_id = $sns_id;

            $this->comment_plugin_share_settings->save($comment_plugin_share_setting);
        }
    }

    /**
     * @param $comment_plugin_id
     */
    public function deleteCommentPluginShareSettings($comment_plugin_id) {
        $comment_plugin_share_settings = $this->getCommentPluginShareSettings($comment_plugin_id);

        foreach ($comment_plugin_share_settings as $comment_plugin_share_setting) {
            $this->comment_plugin_share_settings->delete($comment_plugin_share_setting);
        }
    }

    // CommentPluginActions

    /**
     * @return mixed
     */
    public function createEmptyCommentPluginAction() {
        return $this->comment_plugin_actions->createEmptyObject();
    }

    /**
     * @param $comment_plugin_id
     * @return mixed
     */
    public function getCommentPluginActionByCommentPluginId($comment_plugin_id) {
        $filter = array(
            'comment_plugin_id' => $comment_plugin_id
        );

        return $this->comment_plugin_actions->findOne($filter);
    }

    /**
     * @param $comment_plugin_id
     */
    public function updateCommentPluginAction($comment_plugin_id) {
        $comment_plugin_action = $this->getCommentPluginActionByCommentPluginId($comment_plugin_id);

        if (!$comment_plugin_action) {
            $comment_plugin_action = $this->createEmptyCommentPluginAction();

            $comment_plugin_action->order_no = 1;
            $comment_plugin_action->requirement_flg = CommentPluginAction::REQUIREMENT_FLG_ON;
            $comment_plugin_action->type = CommentPluginAction::COMMENT_PLUGIN_ACTION_TYPE_FREETEXT;
        }

        $comment_plugin_action->comment_plugin_id = $comment_plugin_id;

        $this->comment_plugin_actions->save($comment_plugin_action);

        $this->updateCommentFreeTextAction($comment_plugin_action->id);
    }

    // CommentFreeTextAction

    /**
     * @return mixed
     */
    public function createEmptyCommentFreeTextAction() {
        return $this->comment_free_text_actions->createEmptyObject();
    }

    /**
     * @param $comment_plugin_action_id
     * @return mixed
     */
    public function getCommentFreeTextActionByCommentPluginActionId($comment_plugin_action_id) {
        $filter = array(
            'comment_plugin_action_id' => $comment_plugin_action_id
        );

        return $this->comment_free_text_actions->findOne($filter);
    }

    /**
     * @param $comment_plugin_action_id
     */
    public function updateCommentFreeTextAction($comment_plugin_action_id) {
        $comment_free_text_action = $this->getCommentFreeTextActionByCommentPluginActionId($comment_plugin_action_id);

        if (!$comment_free_text_action) {
            $comment_free_text_action = $this->createEmptyCommentFreeTextAction();
            $comment_free_text_action->text = "";
            $comment_free_text_action->extra_data = "";
        }

        $comment_free_text_action->comment_plugin_action_id = $comment_plugin_action_id;

        $this->comment_free_text_actions->save($comment_free_text_action);
    }

    /**
     * @param $comment_plugin_id
     * @param $user_info
     * @param bool $has_fb_public_action
     * @return array
     */
    public function getUserShareSNSList($comment_plugin_id, $user_info, $has_fb_public_action = false) {
        AAFW::import('jp.aainc.classes.services.SocialAccountService');

        $user_share_sns_list = array();
        $cp_sns_list = $this->getCommentPluginShareSnsList($comment_plugin_id);

        foreach ($user_info->socialAccounts as $social_account) {
            $cur_social_media_id = SocialAccountService::getSocialMediaIdBySocialMediaType($social_account->socialMediaType);
            if ($cur_social_media_id == SocialAccountService::SOCIAL_MEDIA_FACEBOOK && !$has_fb_public_action) {
                continue;
            }

            if (!in_array($cur_social_media_id, $cp_sns_list)) {
                continue;
            }

            $user_share_sns_list[] = $social_account->socialMediaType;
        }

        return $user_share_sns_list;
    }

    /**
     * ユーザ側に表示するコメント数を返す
     * リプライコメント・削除コメントはカウントしない
     * @param $brand_id
     * @param $entry_id
     * @return string
     */
    public function countAvailableCommentByStaticHtmlEntryId ($brand_id, $entry_id) {
        $comment_plugin = $this->getActiveCommentPlugin($brand_id, $entry_id);

        if (!$comment_plugin) {
            return "0";
        }

        //discard_flg = 0という存在しているコメントだけをカウントする
        $params = array(
            'comment_plugin_id' => $comment_plugin->id,
        );

        $result = $this->data_builder->countAvailableCommentByCommentPluginId($params);

        return $result[0]['comment_count'];
    }

    /**
     * @param $brand_id
     * @param $comment_plugin_id
     * @return string
     */
    public function generatePluginCode($brand_id, $comment_plugin_id) {
        $plugin_code_string = CommentPlugin::PLUGIN_CODE_PREFIX . $brand_id . '_' . $comment_plugin_id;
        return substr(base_convert(md5($plugin_code_string), 16, 36), 0, 12);
    }
}