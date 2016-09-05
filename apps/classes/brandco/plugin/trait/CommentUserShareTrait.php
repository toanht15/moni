<?php

trait CommentUserShareTrait {

    protected $comment_user_shares;

    /**
     * @param $comment_user_relation_id
     * @return array
     */
    public function getCommentUserShareSnsList($comment_user_relation_id) {
        $filter = array(
            'comment_user_relation_id' => $comment_user_relation_id
        );

        $share_sns_list = array();
        $comment_user_shares = $this->comment_user_shares->find($filter);
        foreach ($comment_user_shares as $comment_user_share) {
            $share_sns_list[] = $comment_user_share->social_media_id;
        }

        return $share_sns_list;
    }

    /**
     * @param $comment_user_relation_id
     * @param $social_media_id
     * @return mixed
     */
    public function getCommentUserShareByCommentUserRelationIdAndSocialMediaId($comment_user_relation_id, $social_media_id) {
        $filter = array(
            'comment_user_relation_id' => $comment_user_relation_id,
            'social_media_id' => $social_media_id
        );

        return $this->comment_user_shares->findOne($filter);
    }

    /**
     * @param $comment_user_relation_id
     * @param $social_media_ids
     */
    public function createCommentUserShares($comment_user_relation_id, $social_media_ids) {
        foreach ($social_media_ids as $social_media_id) {
            $comment_user_share = $this->comment_user_shares->createEmptyObject();

            $comment_user_share->comment_user_relation_id = $comment_user_relation_id;
            $comment_user_share->social_media_id = $social_media_id;

            $this->comment_user_shares->save($comment_user_share);
        }
    }

    /**
     * @param $comment_user_share
     */
    public function updateCommentUserShare($comment_user_share) {
        $this->comment_user_shares->save($comment_user_share);
    }
}