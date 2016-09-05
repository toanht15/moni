<?php

trait CommentUserLikeTrait {

    protected $comment_user_likes;

    /**
     * @param $user_id
     * @param $comment_user_relation_id
     * @return mixed
     */
    public function getCommentUserLike($user_id, $comment_user_relation_id) {
        $filter = array(
            'user_id' => $user_id,
            'comment_user_relation_id' => $comment_user_relation_id
        );

        return $this->comment_user_likes->findOne($filter);
    }

    /**
     * @param $like_data
     */
    public function updateCommentUserLike($like_data) {
        $comment_user_like = $this->getCommentUserLike($like_data['user_id'], $like_data['comment_user_relation_id']);

        if ($comment_user_like) {
            $this->comment_user_likes->delete($comment_user_like);
        } else {
            $comment_user_like = $this->comment_user_likes->createEmptyObject();

            $comment_user_like->user_id = $like_data['user_id'];
            $comment_user_like->comment_user_relation_id = $like_data['comment_user_relation_id'];

            $this->comment_user_likes->save($comment_user_like);
        }
    }

    /**
     * @param $comment_user_relation_id
     * @return mixed
     */
    public function countCommentUserLike($comment_user_relation_id) {
        $filter = array(
            'comment_user_relation_id' => $comment_user_relation_id
        );

        return $this->comment_user_likes->count($filter);
    }
}