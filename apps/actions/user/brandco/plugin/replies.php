<?php
AAFW::import('jp.aainc.classes.services.base.CommentPluginActionBaseService');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.CommentPluginValidator');

class replies extends BrandcoGETActionBase {

    use CommentPluginActionBaseService;

    protected $ContainerName = 'page';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);

    public function doThisFirst() {
        $this->initService();
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        $cu_relation_id = $this->GET['cu_relation_id'];
        $prev_min_id = $this->GET['prev_min_id'];

        $anchor_id = $this->getAnchorId($this->GET['anchor_cur_id']);
        $exclude_id = $this->getExcludeId($anchor_id);

        $replies = array();
        $comment_user_relation = $this->comment_user_service->getCommentUserRelationById($cu_relation_id);
        $comment_user_replies = $this->comment_user_service->getPublicCommentUserReplies($comment_user_relation->object_id, $exclude_id, $prev_min_id);

        foreach ($comment_user_replies as $comment_user_reply) {
            $reply_data = $this->getCommonData($comment_user_reply->id, CommentUserRelation::OBJECT_TYPE_REPLY);
            $reply_data['comment_text'] = $this->comment_user_service->decodeComment($comment_user_reply->extra_data);

            $replies[] = $reply_data;
        }

        if (count($replies)) {
            $prev_min_id = end(array_values($replies))['id'];
            $remaining_reply_count = $this->comment_user_service->countRemainingCommentUserReplies($comment_user_relation->object_id, $prev_min_id, $anchor_id); // First time only
        } else {
            $remaining_reply_count = 0;
        }

        $response_data = array(
            'replies' => $replies,
            'remaining_reply_count' => $remaining_reply_count
        );

        $json_data = $this->createAjaxResponse("ok", $response_data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    /**
     * @param $anchor_id
     * @return null
     */
    public function getExcludeId($anchor_id) {
        if (Util::isNullOrEmpty($anchor_id)) {
            return null;
        }

        return $anchor_id;
    }
}
