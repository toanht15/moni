<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class CommentList extends aafwWidgetBase {

    public function doService($params = array()){
        /** @var CommentUserService $comment_user_service */
        $comment_user_service = $this->getService('CommentUserService');

        $search_conditions = array(
            'status' => $params['status'],
            'bur_no' => $params['bur_no'],
            'nickname' => $params['nickname'],
            'comment_content' => $params['comment_content'],
            'brand_id' => $params['brand_id'],
            'discard_flg' => $params['discard_flg'],
            'note_status' => $params['note_status'],
            'sns_share' => $params['sns_share'],
            'comment_plugin_ids' => $params['comment_plugin_ids'],
            'from_date' => $params['from_date'],
            'to_date'   => $params['to_date']
        );

        $pager = array(
            'page' => $params['page'],
            'count' => $params['page_limit']
        );

        $order = array(
            'name' => CommentUserRelation::$comment_use_relation_order_kinds[$params['order_kind']],
            'direction' => CommentUserRelation::$comment_use_relation_order_types[$params['order_type']],
        );

        $cu_relation_ids = array();
        $params['comment_list'] = array();
        $comment_list = $comment_user_service->getCommentList($search_conditions, $pager, $order);

        // NOTE foreach change original object values unsafely so passing object values to array just in case
        foreach ($comment_list as $comment) {
            $comment->note_status = Util::isNullOrEmpty($comment->note) ? CommentUserRelation::NOTE_STATUS_INVALID : CommentUserRelation::NOTE_STATUS_VALID;
            $comment->created_time = date('Y/m/d H:i', strtotime($comment->created_at));
            $comment->from = $this->getUserData($comment);
            $comment->share_sns_list = $comment_user_service->getCommentUserShareSnsList($comment->id);
            $comment->like_count = $comment_user_service->countCommentUserLike($comment->id);

            $comment_text = $comment_user_service->decodeComment($comment->extra_data);
            $comment->comment_text = $comment_user_service->cutTextByLine($comment_text, false);
            $comment->comment_url = $this->getCommentUrl($comment);

            if ($comment->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT) {
                // Include approved and rejected comment
                $comment->comment_count = $comment_user_service->countCommentUserRepliesByCommentUserId($comment->object_id);
            }

            $cu_relation_ids[] = $comment->id;
            $params['comment_list'][] = $comment;
        }
        $params['cp_share_sns_list'] = $this->getShareSnsList($params['comment_plugin_ids']);

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);

        $params['is_hide_personal_info'] = !Util::isNullOrEmpty($brand_global_setting) ? true : false;
        if ($params['is_hide_personal_info'] === false) {
            // Fetching Share URL
            /** @var MultiPostSnsQueueService $multi_post_sns_queue_service */
            $multi_post_sns_queue_service = $this->getService('MultiPostSnsQueueService');
            $multi_post_sns_queues = $multi_post_sns_queue_service->getMultiPostSnsQueueByCallback(MultiPostSnsQueue::CALLBACK_UPDATE_COMMENT_USER_SHARE, $cu_relation_ids);
            foreach ($multi_post_sns_queues as $multi_post_sns_queue) {
                $share_url = $multi_post_sns_queue_service->getShareUrl($multi_post_sns_queue['api_result'], $multi_post_sns_queue['social_media_type']);
                if (Util::isNullOrEmpty($share_url)) {
                    continue;
                }
                $params['share_url_list'][$multi_post_sns_queue['callback_parameter']][$multi_post_sns_queue['social_media_type']] = $share_url;
            }
        }

        return $params;
    }

    /**
     * @param $comment_user_relation
     * @return null
     */
    public function getUserData($comment_user_relation) {
        $from_user = null;

        if ($comment_user_relation->anonymous_flg == CommentUserRelation::ANONYMOUS_FLG_OFF) {
            /** @var UserService $user_service */
            $user_service = $this->getService('UserService');
            $from_user = $user_service->getUserPublicInfoByBrandcoUserId($comment_user_relation->user_id);
            $from_user->no = $comment_user_relation->bur_no;

            foreach (CommentPluginShareSetting::$comment_plugin_share_settings as $social_media_id => $value) {
                $sa_id = 'sa_id_' . $social_media_id;
                $sa_friend_count = 'sa_friend_count_' . $social_media_id;

                $from_user->$sa_id = $comment_user_relation->$sa_id;
                $from_user->$sa_friend_count = $comment_user_relation->$sa_friend_count ?: 0;
            }
        }

        return $from_user;
    }

    /**
     * @param $comment_plugin_ids
     * @return array
     */
    public function getShareSnsList($comment_plugin_ids) {
        $share_sns_list = array();
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');

        foreach ($comment_plugin_ids as $comment_plugin_id) {
            $share_sns_list[$comment_plugin_id] = $comment_plugin_service->getCommentPluginShareSnsList($comment_plugin_id);
        }

        return $share_sns_list;
    }

    /**
     * @param $comment
     * @return string
     */
    public function getCommentUrl($comment) {
        if (!Util::isNullOrEmpty($comment->request_url)) {
            return Util::stripQueryString($comment->request_url) . '#cur_id_' . $comment->id;
        }

        $comment_plugin_service = $this->getService('CommentPluginService');
        $comment_plugin = $comment_plugin_service->getCommentPluginById($comment->comment_plugin_id);

        if (Util::isNullOrEmpty($comment_plugin->static_html_entry_id) || $comment_plugin->static_html_entry_id == 0) {
            return '';
        }

        $static_html_entry_service = $this->getService('StaticHtmlEntryService');
        $static_html_entry = $static_html_entry_service->getEntryById($comment_plugin->static_html_entry_id);

        if (Util::isNullOrEmpty($static_html_entry) || !$static_html_entry->isPublic()) {
            return '';
        }

        return $static_html_entry->getUrl() . '#cur_id_' . $comment->id;
    }
}