<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class comments extends BrandcoGETActionBase {

    use CommentPluginActionBaseService;

    protected $ContainerName = 'page';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    public function doThisFirst() {
        $this->initService();
        $this->comment_plugin_id = $this->GET['comment_plugin_id'];
    }

    public function validate() {
        $comment_plugin_validator = new CommentPluginValidator($this->comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        if (!$comment_plugin_validator->isActiveCommentPlugin()) {
            return false;
        }

        return true;
    }

    public function doAction() {
        $comments = array();
        $prev_min_id = $this->GET['prev_min_id'];

        // アンカー周りの処理
        $anchor_id = $this->getAnchorId($this->GET['anchor_cur_id']);
        $exclude_id = $this->getExcludeId($anchor_id);
        $anchor_comment_data = $prev_min_id == 0 ? $this->getAnchorCommentData($anchor_id) : null;

        if (!Util::isNullOrEmpty($anchor_comment_data)) {
            $comments[] = $anchor_comment_data;
        }

        // Public comment count
        $comment_count = $this->comment_user_service->countPublicCommentUsers($this->comment_plugin_id);
        $comment_users = $this->comment_user_service->getPublicCommentUsers($this->comment_plugin_id, $prev_min_id, $exclude_id);

        foreach ($comment_users as $comment_user) {
            $comments[] = $this->getCommentData($comment_user, $anchor_id);
        }

        // Check load_more flag
        if (count($comments)) {
            $prev_min_id = end(array_values($comments))['id'];
            $remaining_comment_count = $this->comment_user_service->countPublicCommentUsers($this->comment_plugin_id, $prev_min_id);
            if (!Util::isNullOrEmpty($exclude_id)) {
                $remaining_comment_count -= 1;
            }
            $load_more_flg = $remaining_comment_count > 0;
        } else {
            $load_more_flg = false;
        }

        $response_data = array(
            'user' => $this->getUserData(),
            'comments' => $comments,
            'load_more_flg' => $load_more_flg,
            'comment_count' => $comment_count
        );

        $json_data = $this->createAjaxResponse("ok", $response_data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * @param $anchor_id
     * @return array|null
     */
    public function getAnchorCommentData($anchor_id) {
        if (Util::isNullOrEmpty($anchor_id)) {
            return null;
        }

        $anchor_cu_relation = $this->comment_user_service->getCommentUserRelationById($anchor_id);
        if ($anchor_cu_relation->isRejected() === true) {
            return null;
        }

        if ($anchor_cu_relation->object_type == CommentUserRelation::OBJECT_TYPE_REPLY) {
            $anchor_comment_user_reply = $this->comment_user_service->getCommentUserReplyById($anchor_cu_relation->object_id);
            $anchor_comment_relation = $this->comment_user_service->getCommentUserRelation($anchor_comment_user_reply->comment_user_id, CommentUserRelation::OBJECT_TYPE_COMMENT);
            if ($anchor_comment_relation->isRejected() === true) {
                return null;
            }

            $anchor_comment_user = $this->comment_user_service->getCommentUserById($anchor_comment_user_reply->comment_user_id);
        } else {
            $anchor_comment_user_reply = null;
            $anchor_comment_user = $this->comment_user_service->getCommentUserById($anchor_cu_relation->object_id);
        }

        if ($anchor_comment_user->comment_plugin_id != $this->comment_plugin_id) {
            return null;
        }

        $comment_data = $this->getCommentData($anchor_comment_user, $anchor_id);

        if (!Util::isNullOrEmpty($anchor_comment_user_reply)) {
            $reply_data = $this->getCommonData($anchor_comment_user_reply->id, CommentUserRelation::OBJECT_TYPE_REPLY);

            $comment_text = $this->comment_user_service->decodeComment($anchor_comment_user_reply->extra_data);
            $reply_data['original_text'] = $comment_text;
            $reply_data['comment_text'] = $this->comment_user_service->cutTextByLine($comment_text, true, 'もっとみる');

            array_unshift($comment_data['replies'], $reply_data);

            if ($comment_data['remaining_reply_count'] != 0) {
                $comment_data['remaining_reply_count'] -= 1;
            }
        }

        return $comment_data;
    }

    /**
     * @param $anchor_id
     * @return null
     */
    public function getExcludeId($anchor_id) {
        if (Util::isNullOrEmpty($anchor_id)) {
            return null;
        }

        $anchor_cu_relation = $this->comment_user_service->getCommentUserRelationById($anchor_id);
        if ($anchor_cu_relation->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT) {
            return $anchor_id;
        }

        $anchor_reply = $this->comment_user_service->getCommentUserReplyById($anchor_cu_relation->object_id);
        $anchor_comment_relation = $this->comment_user_service->getCommentUserRelation($anchor_reply->comment_user_id, CommentUserRelation::OBJECT_TYPE_COMMENT);

        return $anchor_comment_relation->id;
    }

    /**
     * @param $comment_user
     * @param $anchor_id
     * @return array
     */
    public function getCommentData($comment_user, $anchor_id) {
        $comment_data = $this->getCommonData($comment_user->id, CommentUserRelation::OBJECT_TYPE_COMMENT);

        $comment_free_text_user = $this->comment_user_service->getCommentFreeTextUser($comment_user->id);
        $comment_text = $this->comment_user_service->decodeComment($comment_free_text_user->extra_data);
        $comment_data['original_text'] = $comment_text;
        $comment_data['comment_text'] = $this->comment_user_service->cutTextByLine($comment_text, true, 'もっとみる');

        $comment_data['replies'] = array();
        $comment_user_replies = $this->comment_user_service->getPublicCommentUserReplies($comment_user->id, $anchor_id);

        foreach ($comment_user_replies as $comment_user_reply) {
            $reply_data = $this->getCommonData($comment_user_reply->id, CommentUserRelation::OBJECT_TYPE_REPLY);

            $comment_text = $this->comment_user_service->decodeComment($comment_user_reply->extra_data);
            $reply_data['original_text'] = $comment_text;
            $reply_data['comment_text'] = $this->comment_user_service->cutTextByLine($comment_text, true, 'もっとみる');

            $comment_data['replies'][] = $reply_data;
        }

        if (count($comment_data['replies'])) {
            $prev_min_id = end(array_values($comment_data['replies']))['id'];
            $comment_data['remaining_reply_count'] = $this->comment_user_service->countRemainingCommentUserReplies($comment_user->id, $prev_min_id, $anchor_id);
        } else {
            $comment_data['remaining_reply_count'] = 0;
        }

        return $comment_data;
    }
}
