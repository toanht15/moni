<?php

trait CommentUserMentionTrait {

    protected $comment_user_mentions;

    /**
     * @param $comment_user_relation_id
     * @param $mentioned_object_id
     */
    public function createCommentUserMention($comment_user_relation_id, $mentioned_object_id) {
        if (Util::isNullOrEmpty($comment_user_relation_id) || Util::isNullOrEmpty($mentioned_object_id)) {
            return;
        }

        $mentioned_cu_relation = $this->getCommentUserRelationById($mentioned_object_id);
        if (Util::isNullOrEmpty($mentioned_cu_relation)) {
            return;
        }

        $comment_user_mention = $this->comment_user_mentions->createEmptyObject();

        $comment_user_mention->comment_user_relation_id = $comment_user_relation_id;
        $comment_user_mention->mentioned_user_id = $mentioned_cu_relation->user_id;

        $this->comment_user_mentions->save($comment_user_mention);
    }
}