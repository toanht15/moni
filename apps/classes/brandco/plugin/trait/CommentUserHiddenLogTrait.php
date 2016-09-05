<?php

trait CommentUserHiddenLogTrait {
    protected $comment_user_hidden_logs;

    /**
     * @param $comment_user_relation_id
     * @param $user_id
     */
    public function createCommentUserHiddenLog($comment_user_relation_id, $user_id) {
        $comment_user_hidden_log = $this->comment_user_hidden_logs->createEmptyObject();

        $comment_user_hidden_log->user_id = $user_id;
        $comment_user_hidden_log->comment_user_relation_id = $comment_user_relation_id;

        $this->comment_user_hidden_logs->save($comment_user_hidden_log);
    }

    /**
     * @param $comment_user_relation_id
     * @param $user_id
     * @return mixed
     */
    public function getCommentUserHiddenLog($comment_user_relation_id, $user_id) {
        $filter = array(
            'comment_user_relation_id' => $comment_user_relation_id,
            'user_id' => $user_id
        );

        return $this->comment_user_hidden_logs->findOne($filter);
    }

    /**
     * @param $comment_user_relation_id
     * @param $user_id
     */
    public function deleteCommentUserHiddenLog($comment_user_relation_id, $user_id) {
        $comment_user_hidden_log = $this->getCommentUserHiddenLog($comment_user_relation_id, $user_id);
        $this->comment_user_hidden_logs->delete($comment_user_hidden_log);
    }

    /**
     * @param $comment_user_relation_id
     * @param $user_id
     * @return bool
     */
    public function isHiddenComment($comment_user_relation_id, $user_id) {
        $comment_user_hidden_log = $this->getCommentUserHiddenLog($comment_user_relation_id, $user_id);

        return !Util::isNullOrEmpty($comment_user_hidden_log);
    }
}